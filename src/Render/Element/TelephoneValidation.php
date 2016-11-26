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
 *   '#element_validate' => [['Drupal\telephone_validation\Render\Element\TelephoneValidation', 'validateTel']],
 *   '#element_validate_settings' => [
 *     'valid_format' => PhoneNumberFormat::E164,
 *     // All countries are valid.
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
    $service = \Drupal::service('telephone_validation.validator');

    $value = $element['#value'];
    $form_state->setValueForElement($element, $value);

    if ($value !== '' && !$service->isValid($value, $element['#element_validate_settings']['format'], $element['#element_validate_settings']['country'])) {
      $form_state->setError($element, t('The phone number %phone is not valid.', array('%phone' => $value)));
    }
  }
}
