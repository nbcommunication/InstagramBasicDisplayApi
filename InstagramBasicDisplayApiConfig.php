<?php

/**
 * Instagram Basic Display API Configuration
 * 
 * @todo remove user auth
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
		$config->scripts->add($config->urls($instagram) . "{$this->className}.js");

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
					$this->_("Token Expires"),
				]);
				foreach(json_decode($instagram->accessData, 1) as $username => $data) {

					$profile = $instagram->getProfile($username);
					if(!is_array($profile)) {
						$profile = [
							"account_type" => "",
							"id" => (isset($data["user_id"]) ? $data["user_id"] : ""),
							"media_count" => "",
						];
					}
					$expires = isset($data["expires_in"]) ? $data["expires_in"] : 0;

					$table->row([
						"<a href='https://www.instagram.com/$username' target='_blank'>$username</a>",
						$profile["account_type"],
						$profile["id"],
						$profile["media_count"],
						($expires ?
							"<span title='" . $datetime->date("d/m/Y H:i", $expires) . "'>" .
								$datetime->date("rel", $expires) .
							"</span>" :
							""
						),
					]);
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
			$query->bindValue(":name", wireClassName($instagram, false) . "__%");
			$query->execute();
			$numCaches = $query->rowCount();

			$inputfields->add([
				"type" => "integer",
				"name" => "cacheTime",
				"label" => $this->_("Cache"),
				"description" => $this->_("The number of seconds an API response should be cached for."),
				"notes" => ($numCaches ? sprintf($this->_n("There is currently %d cached request.", "There are currently %d cached requests.", $numCaches), $numCaches) : ""),
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
