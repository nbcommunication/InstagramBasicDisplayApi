<?php

/**
 * Instagram Basic Display API Configuration
 * 
 * @todo Option to remove authorized user
 * @todo Ability to reorder users (to change default)
 * @todo If user is no longer authorized, remove and log
 * 
 */

class InstagramBasicDisplayApiConfig extends ModuleConfig {

	/**
	 * Returns default values for module variables
	 *
	 * @return array
	 *
	 */
	public function getDefaults() {
		return [
			"cacheTime" => 3600,
		];
	}

	/**
	 * Returns inputs for module configuration
	 *
	 * @return InputfieldWrapper
	 *
	 */
	public function getInputfields() {

		$config = $this->wire("config");
		$input = $this->wire("input");
		$modules = $this->wire("modules");
		$inputfields = parent::getInputfields();

		// Get the module
		$instagram = $modules->get(str_replace("Config", "", $this->className));

		if($input->post("clearCache")) {
			// Clear the Cache
			$this->wire("cache")->deleteFor($instagram);
			$this->message($this->_("Cache cleared"));
			$this->wire("session")->redirect($input->url(true));
		}

		// Add script
		try {
			$urlModule = $config->urls($instagram); // PW 3
		} catch(Exception $e) {
			$urlModule = $config->urls->root . "site/modules/$instagram->className/";
		}
		$config->scripts->add("$urlModule{$this->className}.js");

		// Path to setting in Facebook Developer App Dashboard
		$pathSettings = "*" . $this->_("App Dashboard > Products > Instagram > Basic Display") . "*";

		// Access Data
		$inputfields->add([
			"type" => "textarea",
			"name" => "accessData",
			"label" => $this->_("Access Data"),
			"attr" => ["disabled" => true],
			"icon" => "key",
			"rows" => (substr_count($instagram->accessData, "},{") + 1) * 5,
			"collapsed" => $input->get->bool("accessData") ? 0 : 4,
		]);

		// Authentication
		$fieldset = $modules->get("InputfieldFieldset");
		$fieldset->label = $this->_("Authentication");
		$fieldset->notes = sprintf($this->_("These values are displayed in %s."), $pathSettings);
		$fieldset->icon = "key";
		$fieldset->collapsed = 5;

		// Client ID
		$fieldset->add([
			"type" => "text",
			"name" => "clientId",
			"label" => $this->_("Instagram App ID"),
			"icon" => "star",
			"columnWidth" => 50,
		]);

		// Client Secret
		$fieldset->add([
			"type" => "text",
			"name" => "clientSecret",
			"label" => $this->_("Instagram App Secret"),
			"icon" => "star-o",
			"columnWidth" => 50,
			"attr" => ["type" => "password"],
		]);

		if($instagram->clientId && $instagram->clientSecret) {

			// Authorized Accounts
			if($instagram->accessData) {

				$datetime = $this->wire("datetime");

				$table = $modules->get("MarkupAdminDataTable");
				$table->setEncodeEntities(false);
				$table->headerRow([
					$this->_("Username"),
					$this->_("Account Type"),
					$this->_("User ID"),
					$this->_("Media Count"),
					$this->_("Token Renews"),
				]);
				$dateFormat = "d/m/Y H:i";
				foreach(json_decode($instagram->accessData, 1) as $username => $data) {

					$profile = $instagram->getProfile($username);
					if(is_array($profile) && isset($profile["id"])) {
						$expires = isset($data["expires_in"]) ? $data["expires_in"] : 0;
						$table->row([
							"<a href='https://www.instagram.com/$username' target='_blank'>$username</a>",
							$profile["account_type"],
							$profile["id"],
							$profile["media_count"],
							($expires ?
								"<span title='" . (isset($datetime) ? $datetime->date($dateFormat, $expires) : date($dateFormat, $expires)) . "'>" .
									(isset($datetime) ? $datetime->date("rel", $expires) : date($dateFormat, $expires)) .
								"</span>" :
								""
							),
						]);
					} else {
						$instagram->error(sprintf($this->_("The user profile for %s could not be retrieved."), $username));
					}
				}

				$inputfields->add([
					"type" => "markup",
					"label" => $this->_("Authorized Accounts"),
					"icon" => "instagram",
					"value" => $table->render(),
				]);
			}

			// Add Authentication Fieldset
			$inputfields->add($fieldset);

			// URIs
			$fieldset = $modules->get("InputfieldFieldset");
			$fieldset->label = $this->_("URIs");
			$fieldset->icon = "link";
			$fieldset->collapsed = $instagram->accessData ? 1 : 0;

			$copied = [
				"data-copy" => json_encode([
					"on" => $this->_("Copied"),
					"off" => $this->_("Copy to Clipboard"),
				]),
			];

			// Redirect URI
			$fieldset->add([
				"type" => "text",
				"name" => "redirectUri",
				"label" => $this->_("Redirect URI"),
				"notes" => sprintf(
					$this->_("Please add this to the list of **Valid OAuth Redirect URIs** in %s prior to visiting the Authorization Code URI below."),
					$pathSettings
				),
				"value" => $instagram->getRedirectUri(),
				"icon" => "link",
				"attr" => $copied,
			]);

			// Authorization Code URI
			$fieldset->add([
				"type" => "text",
				"name" => "authUri",
				"label" => $this->_("Authorization Code URI"),
				"notes" => $this->_("When a client visits this link they will be asked to authorize your application. Once this is complete they will be redirected back to this website and the authorization code will be saved."),
				"value" => $instagram->getAuthUri(),
				"icon" => "link",
				"attr" => $copied,
			]);

			$inputfields->add($fieldset);

			// Cache Time
			$query = $this->wire("database")->prepare("SELECT name FROM caches WHERE name LIKE :name");
			$query->bindValue(":name", "{$instagram->className}__%");
			$query->execute();
			$numCaches = $query->rowCount();

			$inputfields->add([
				"type" => "integer",
				"name" => "cacheTime",
				"label" => $this->_("Cache"),
				"description" => sprintf(
					$this->_('The number of seconds an API response should be cached for. If set to %1$s a cache time of %2$s will be used.'),
					"`0`",
					"`3600`"
				),
				"notes" => ($numCaches ? sprintf(
					$this->_n("There is currently %d cached request.", "There are currently %d cached requests.",
					$numCaches
				), $numCaches) : ""),
				"icon" => "files-o",
				"collapsed" => 1,
				"appendMarkup" => ($numCaches ? 
					$modules->get("InputfieldSubmit")
						->attr("name+id", "clearCache")
						->attr("value", $this->_("Clear Cache"))
						->render() : 
					""
				),
			]);

		} else {

			$inputfields->add($fieldset);
		}

		return $inputfields;
	}
}
