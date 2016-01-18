<?php
/**
 * @file
 * Contains \Drupal\telephone_validation\TelephoneValidationSettingsForm
 */
namespace Drupal\telephone_validation;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use libphonenumber\PhoneNumberFormat;

/**
 * Configure hello settings for this site.
 */
class TelephoneValidationSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'telephone_validation_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'telephone_validation.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('telephone_validation.settings');
    $service = \Drupal::service('tel.validator');

    $form['telephone_validation_valid_format'] = array(
      '#type' => 'select',
      '#title' => $this->t('Valid format'),
      '#default_value' => $config->get('valid_format'),
      '#options' => [
        PhoneNumberFormat::E164 => $this->t('E164'),
        PhoneNumberFormat::NATIONAL => $this->t('National'),
      ],
    );

    // @todo: Change field based on telephone_validation_valid_format value.
    $form['telephone_validation_valid_countries'] = array(
      '#type' => 'select',
      '#title' => $this->t('Valid countries'),
      '#description' => t('If no country selected all countries are valid.'),
      '#default_value' => $config->get('valid_countries'),
      '#multiple' => $config->get('valid_format') === PhoneNumberFormat::NATIONAL ? FALSE : TRUE,
      '#options' => $service->getCountryList(),
      '#prefix' => '<div id="telephone-validation-valid-countries">',
      '#suffix' => '</div>',
    );

    $form['telephone_validation_store_format'] = array(
      '#type' => 'select',
      '#title' => t('Store format'),
      '#description' => t('It is highly recommended to store data in E164 format. That is international format with no whitespaces preceded by plus and country code.'),
      '#default_value' => $config->get('store_format'),
      '#options' => array(
        PhoneNumberFormat::E164 => t('E164'),
        PhoneNumberFormat::NATIONAL => t('National'),
        PhoneNumberFormat::INTERNATIONAL => t('International'),
      ),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('telephone_validation.settings')
      ->set('valid_format', $form_state->getValue('telephone_validation_valid_format'))
      ->set('valid_countries', $form_state->getValue('telephone_validation_valid_countries'))
      ->set('store_format', $form_state->getValue('telephone_validation_store_format'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
