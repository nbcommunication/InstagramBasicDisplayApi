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
			'cacheTime' => 3600,
			'limit' => 24,
		];
	}

	/**
	 * Returns inputs for module configuration
	 *
	 * @return InputfieldWrapper
	 *
	 */
	public function getInputfields() {

		$config = $this->wire('config');
		$input = $this->wire('input');
		$modules = $this->wire('modules');
		$sanitizer = $this->wire('sanitizer');
		$inputfields = parent::getInputfields();

		// Get the module
		$instagram = $modules->get(str_replace('Config', '', $this->className));

		if($input->post('_addUsername') && $input->post('_addToken')) {

			// Add user
			$username = $sanitizer->pageName(trim($input->post('_addUsername')));
			$token = $sanitizer->text(trim($input->post('_addToken')));
			if($username && $token && $instagram->addUserAccount($username, $token)) {
				$instagram->message(sprintf($this->_('%s has been added.'), $username));
			} else {
				$instagram->error(sprintf($this->_('Could not add user account %s'), $username));
			}

			$this->_addUsername = '';
			$this->_addToken = '';

		} else if($input->post('_removeAccount')) {

			// Remove user(s)
			foreach($input->post('_removeAccount') as $username) {
				$username = $sanitizer->pageName($username);
				if($username && $instagram->removeUserAccount($username)) {
					$instagram->message(sprintf($this->_('%s has been removed.'), $username));
				} else {
					$instagram->error(sprintf($this->_('Could not remove user account %s'), $username));
				}
			}

			$this->_removeAccount = [];

		} else if($input->post('clearCache')) {
			// Clear the Cache
			$this->wire('cache')->deleteFor($instagram);
			$this->message($this->_('Cache cleared'));
			$this->wire('session')->redirect($input->url(true));
		}

		// Add assets
		$urlAsset = "{$config->urls->root}site/modules/$instagram->className/{$this->className}";
		$config->styles->add("$urlAsset.css");
		$config->scripts->add("$urlAsset.js");

		// Get User Accounts
		$accounts = $instagram->getUserAccounts(true);

		// Add an Instagram User Account
		$fieldset = $modules->get('InputfieldFieldset');
		$fieldset->label = $this->_('Add an Instagram User Account');
		$fieldset->notes = implode("\n- ", [
			$this->_('Before adding a user you must first:'),
			sprintf($this->_('Add the user as an Instagram Tester in %s'), '*App Dashboard > Roles > Instagram Testers*'),
			sprintf($this->_('Get the user to approve the request in %s'), '*Settings > Apps and Websites*'),
			sprintf($this->_('Generate a User Token in %s'), '*App Dashboard > Products > Instagram > Basic Display > User Token Generator*'),
		]);
		$fieldset->icon = 'plus-circle';
		$fieldset->collapsed = count($accounts) ? 1 : 0;

		$fieldset->add([
			'type' => 'text',
			'name' => '_addUsername',
			'label' => $this->_('Username'),
			'value' => '',
			'icon' => 'instagram',
			'columnWidth' => 25,
		]);

		$fieldset->add([
			'type' => 'text',
			'name' => '_addToken',
			'label' => $this->_('Token'),
			'value' => '',
			'icon' => 'key',
			'columnWidth' => 75,
		]);

		$inputfields->add($fieldset);

		if(count($accounts)) {

			$datetime = $this->wire('datetime');

			$table = $modules->get('MarkupAdminDataTable');
			$table->setEncodeEntities(false);
			$table->setSortable(false);
			$table->headerRow([
				$this->_('Username'),
				//$this->_('Account Type'),
				$this->_('User ID'),
				$this->_('Media Count'),
				$this->_('Token Renews'),
				'',
			]);

			foreach($accounts as $username => $account) {

				$id = "remove_$username";
				$remove = $modules->get('InputfieldCheckbox');
				$remove->attr('hidden', true);
				$remove->addClass('remove-account');
				foreach([
					'name' => '_removeAccount[]',
					'id' => $id,
					'value' => $username,
					'label' => wireIconMarkup('trash'),
					'entityEncodeLabel' => false,
					'labelAttrs' => ['for' => $id],
				] as $key => $value) {
					$remove->set($key, $value);
				};

				$renews = strtotime($account['token_renews']);
				$table->row([
					"<a href='https://www.instagram.com/$username' target='_blank' rel='noopener noreferrer'>$username</a>",
					//$account['account_type'],
					$account['user_id'],
					$account['media_count'],
					"<span title='$account[token_renews]'>" .
						(isset($datetime) ? $datetime->date('rel', $renews) : date('d/m/Y H:i', $renews)) .
					'</span>',
					$remove->render(),
				]);
			}

			$inputfields->add([
				'type' => 'markup',
				'id' => 'instagram-accounts',
				'label' => $this->_('Authorized Accounts'),
				'icon' => 'instagram',
				'value' => $table->render(),
			]);

			$inputfields->add([
				'type' => 'integer',
				'name' => 'limit',
				'label' => $this->_('Limit'),
				'description' => $this->_('The default number of items to return:'),
				'icon' => 'th',
				'collapsed' => 1,
			]);
		}

		// Cache Time
		$query = $this->wire('database')->prepare('SELECT name FROM caches WHERE name LIKE :name');
		$query->bindValue(':name', "{$instagram->className}__%");
		$query->execute();
		$numCaches = $query->rowCount();

		$inputfields->add([
			'type' => 'integer',
			'name' => 'cacheTime',
			'label' => $this->_('Cache'),
			'description' => sprintf(
				$this->_('The number of seconds an API response should be cached for. If set to %1$s or %2$s (1 week), a default cache time of %3$s will be used.'),
				'`0`',
				'>= `604800`',
				'`3600`'
			),
			'notes' => ($numCaches ? sprintf(
				$this->_n('There is currently %d cached request.', 'There are currently %d cached requests.',
				$numCaches
			), $numCaches) : ''),
			'icon' => 'files-o',
			'collapsed' => 1,
			'appendMarkup' => ($numCaches ?
				$modules->get('InputfieldSubmit')
					->attr('name+id', 'clearCache')
					->attr('value', $this->_('Clear Cache'))
					->render() :
				''
			),
		]);

		return $inputfields;
	}
}
