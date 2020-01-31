<?php

/**
 * Instagram Basic Display API Configuration
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
			"userMedia" => 1,
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
		$modules = $this->wire("modules");
		$inputfields = parent::getInputfields();

		$instagram = $modules->get(str_replace("Config", "", $this->className));

		// Add script
		$config->scripts->add($config->urls($instagram) . "{$this->className}.js");

		// Client ID
		$clientId = $modules->get("InputfieldText");
		$clientId->name = "clientId";
		$clientId->label = $this->_("Instagram App ID");
		$clientId->notes = $this->_("This is displayed in **App Dashboard > Products > Instagram > Basic Display**.");
		$clientId->icon = "star";
		$clientId->columnWidth = 50;
		$inputfields->add($clientId);

		// Client Secret
		$clientSecret = $modules->get("InputfieldText");
		$clientSecret->name = "clientSecret";
		$clientSecret->label = $this->_("Instagram App Secret");
		$clientSecret->notes = $this->_("This is displayed in **App Dashboard > Products > Instagram > Basic Display**.");
		$clientSecret->icon = "star-o";
		$clientSecret->columnWidth = 50;
		$clientSecret->attr("type", "password");
		$inputfields->add($clientSecret);

		// Scope
		$userMedia = $modules->get("InputfieldCheckbox");
		$userMedia->name = "userMedia";
		$userMedia->label = $this->_("Access the user's media?");
		$userMedia->notes = sprintf($this->_('If enabled, the %1$s scope is added to the authentication request. The %2$s scope is required by default.'), "`user_media`", "`user_profile`");
		$userMedia->value = 1;
		$userMedia->icon = "picture-o";
		$inputfields->add($userMedia);

		// Authorization Code URI
		$authorizationCodeUri = $modules->get("InputfieldText");
		$authorizationCodeUri->name = "authorizationCodeUri";
		$authorizationCodeUri->label = $this->_("Authorization Code URI");
		$authorizationCodeUri->value = $instagram->getAuthorizationCodeUri();
		$authorizationCodeUri->icon = "link";
		$authorizationCodeUri->collapsed = 1;
		$inputfields->add($authorizationCodeUri);

		// Access Data
		$accessData = $modules->get("InputfieldTextarea");
		$accessData->name = "accessData";
		$accessData->label = $this->_("Access Data");
		$accessData->notes = "Please do not edit. This is for reference only and will be hidden in future versions.";
		$accessData->icon = "key";
		$accessData->collapsed = 1;
		$inputfields->add($accessData);

		return $inputfields;
	}
}
