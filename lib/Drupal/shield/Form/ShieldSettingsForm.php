<?php

/**
 * @file
 * Contains \Drupal\shield\Form\ShieldSettingsForm.
 */

namespace Drupal\shield\Form;

use Drupal\Core\Form\ConfigFormBase;

/**
 * Displays the shield settings form.
 */
class ShieldSettingsForm extends ConfigFormBase {

	/**
	 * Implements \Drupal\Core\Form\FormInterface::getFormID().
	 */
	public function getFormID() {
		return 'shield_admin_settings';
	}

	/**
	 * Implements \Drupal\Core\Form\FormInterface::buildForm().
	 */
	public function buildForm(array $form, array &$form_state) {
		$config = $this->config('shield.settings');
		$settings = $config->get();

		$form['description'] = array(
			'#type' => 'item',
			'#title' => t('Shield settings'),
			'#description' => t('Set up credentials for an authenticated user. You can also decide whether you want to print out the credentials or not.'),
		);

		$form['general'] = array(
			'#type' => 'fieldset',
			'#title' => t('General settings'),
		);

		$form['general']['shield_allow_cli'] = array(
			'#type' => 'checkbox',
			'#title' => t('Allow command line access'),
			'#description' => t('When the site is accessed from command line (e.g. from Drush, cron), the shield should not work.'),
			'#default_value' => $settings['shield_allow_cli'],
		);

		$form['credentials'] = array(
			'#type' => 'fieldset',
			'#title' => t('Credentials'),
		);

		$form['credentials']['shield_user'] = array(
			'#type' => 'textfield',
			'#title' => t('User'),
			'#default_value' => $settings['shield_user'],
			'#description' => t('Live it blank to disable authentication.')
		);

		$form['credentials']['shield_pass'] = array(
			'#type' => 'textfield',
			'#title' => t('Password'),
			'#default_value' => $settings['shield_pass'],
		);

		$form['shield_print'] = array(
			'#type' => 'textfield',
			'#title' => t('Authentication message'),
			'#description' => t("The message to print in the authentication request popup. You can use [user] and [pass] to print the user and the password respectively. You can leave it empty, if you don't want to print out any special message to the users."),
			'#default_value' => $settings['shield_print'],
		);

		return parent::buildForm($form, $form_state);
	}

	/**
	 * Implements \Drupal\Core\Form\FormInterface:validateForm()
	 */
	public function validateForm(array &$form, array &$form_state) {

		parent::validateForm($form, $form_state);
	}

	/**
	 * Implements \Drupal\Core\Form\FormInterface:submitForm()
	 */
	public function submitForm(array &$form, array &$form_state) {
		$config = $this->configFactory->get('shield.settings');

		$form_values = $form_state['values'];

		$config
			->set('shield_allow_cli', $form_values['shield_allow_cli'])
			->set('shield_user', $form_values['shield_user'])
			->set('shield_pass', $form_values['shield_pass'])
			->set('shield_print', $form_values['shield_print'])
			->save();

		parent::submitForm($form, $form_state);
	}

}
