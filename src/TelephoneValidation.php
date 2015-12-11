<?php

/**
 *
 */

namespace Drupal\telephone_validation;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class TelephoneValidation {

  public function __construct() {
    $this->phone_utils = PhoneNumberUtil::getInstance();
  }

  public function isValid($number, $settings) {

    try {
      // Get default country.
      $default_region = ($settings['#validation_format'] === PhoneNumberFormat::NATIONAL) ? reset($settings['#validation_countries']) : NULL;
      // Parse object.
      $number_object = $this->phone_utils->parse($number, $default_region);
    }
    catch (\Exception $e) {
      return FALSE;
    }
    // Perform basic validation.
    if (!$this->phone_utils->isValidNumber($number_object)) {
      return FALSE;
    }

    // If #validation_countries is not empty and default region can be loaded
    // do region matching validation.
    // This condition is always TRUE for national phone number format.
    if (!empty($settings['#validation_countries']) && $default_region = $this->phone_utils->getRegionCodeForNumber($number)) {
      // If number should belong to one of selected countries.
      // This condition is always TRUE for national phone number format.
      if (!isset($settings['#validation_countries'][$default_region])) {
        return FALSE;
      }
    }

    return TRUE;
  }

}