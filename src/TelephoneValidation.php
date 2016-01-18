<?php

/**
 *
 */

namespace Drupal\telephone_validation;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class TelephoneValidation {

  /**
   * @var \libphonenumber\PhoneNumberUtil
   */
  public $phone_utils;

  /**
   * TelephoneValidation constructor.
   *
   * Initialize PhoneNumberUtil.
   */
  public function __construct() {
    $this->phone_utils = PhoneNumberUtil::getInstance();
  }

  /**
   * Check if number is valid for given settings.
   *
   * @param $number
   *   Phone number.
   * @param array $settings
   *   Settings array.
   *
   * @return bool
   *   Valid or not.
   */
  public function isValid($number, array $settings) {

    try {
      // Get default country.
      $default_region = ($settings['valid_format'] === PhoneNumberFormat::NATIONAL) ? reset($settings['valid_countries']) : NULL;
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
    if (!empty($settings['valid_countries']) && $default_region = $this->phone_utils->getRegionCodeForNumber($number_object)) {
      // If number should belong to one of selected countries.
      // This condition is always TRUE for national phone number format.
      if (!isset($settings['valid_countries'][$default_region])) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Get list of countries with country code and leading digits.
   *
   * @return array
   *   Flatten array you can use directly in select lists.
   */
  public function getCountryList() {
    $phone_util = PhoneNumberUtil::getInstance();
    $regions = array();
    foreach (\Drupal::service('country_manager')->getList() as $region => $name) {
      $region_meta = $phone_util->getMetadataForRegion($region);
      if (is_object($region_meta)) {
        $regions[$region] = $name . ' - +' . $region_meta->getCountryCode() . ' ' . $region_meta->getLeadingDigits();
      }
    }
    return $regions;
  }

}