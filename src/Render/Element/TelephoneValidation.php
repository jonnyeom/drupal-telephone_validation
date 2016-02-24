<?php

/**
 * @file
 * Contains \Drupal\telephone_validation\Render\Element\TelephoneValidation.
 */

namespace Drupal\telephone_validation\Render\Element;

use Drupal\Core\Render\Element\Tel;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides validation to input element type of "tel".
 *
 * Usage example:
 * @code
 * $form['phone'] = array(
 *   '#type' => 'tel',
 *   '#title' => t('Phone'),
 *   '#element_validate_settings' => [
 *     'valid_format' => PhoneNumberFormat::E164,
 *     'valid_countries' => [],
 *   ],
 * );
 * @endcode
 *
 * @see \Drupal\Core\Render\Element\Tel
 *
 * @FormElement("tel")
 */
class TelephoneValidation extends Tel {

  /**
   * Form element validation handler for #type 'tel'.
   *
   * Note that #maxlength and #required is validated by _form_validate() already.
   */
  public static function validateTel(&$element, FormStateInterface $form_state, &$complete_form) {

    $service = \Drupal::service('tel.validator');
    $config = \Drupal::config('telephone_validation.settings');

    $value = trim($element['#value']);
    $form_state->setValueForElement($element, $value);

    $settings = [
      'valid_format' => isset($element['#element_validate_settings']['valid_format']) ? $element['#element_validate_settings']['valid_format'] : $config->get('valid_format'),
      'valid_countries' => isset($element['#element_validate_settings']['valid_countries']) ? $element['#element_validate_settings']['valid_countries'] : $config->get('valid_countries'),
      'store_format' => isset($element['#element_validate_settings']['store_format']) ? $element['#element_validate_settings']['store_format'] : $config->get('store_format'),
    ];

    if ($value !== '' && !$service->isValid($value, $settings)) {
      $form_state->setError($element, t('The phone number %phone is not valid.', array('%phone' => $value)));
    }
    elseif ($value !== '') {
      // If valid - set correct format.
      $form_state->setValueForElement($element, $service->storeFormat($value, $settings));
    }
  }

}
