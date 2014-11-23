<?php

/**
 * @file
 * Contains \Drupal\shield\Form\ShieldSettingsForm.
 */

namespace Drupal\shield\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Path\PathValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure site information settings for this site.
 */
class ShieldSettingsForm extends ConfigFormBase {

  /**
   * The path alias manager.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The path validator.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * Constructs a SiteInformationForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Path\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AliasManagerInterface $alias_manager, PathValidatorInterface $path_validator) {
    parent::__construct($config_factory);

    $this->aliasManager = $alias_manager;
    $this->pathValidator = $path_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('path.alias_manager'),
      $container->get('path.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'shield_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $shield_config = $this->config('shield.settings');
    $settings = $shield_config->get();

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
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('shield.settings')
      ->set('shield_allow_cli', $form_state->getValue('shield_allow_cli'))
      ->set('shield_user', $form_state->getValue('shield_user'))
      ->set('shield_pass', $form_state->getValue('shield_pass'))
      ->set('shield_print', $form_state->getValue('shield_print'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}

