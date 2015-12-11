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
 *   '#validation_format' => PhoneNumberFormat::E164,
 *   '#validation_countries' => [],
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
    $value = trim($element['#value']);
    $form_state->setValueForElement($element, $value);
    $settings = [
      '#validation_format' => $element['#validation_format'],
      '#validation_countries' => $element['#validation_countries'],
    ];

    if ($value !== '' && !\Drupal::service('tel.validator')->isValid($value, $settings)) {
      $form_state->setError($element, t('The phone number %phone is not valid.', array('%phone' => $value)));
    }
  }

}
