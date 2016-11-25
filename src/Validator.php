<?php

/**
 *
 */

namespace Drupal\telephone_validation;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class Validator {

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
   * @param $value
   *   Phone number.
   * @param $format
   *   Supported input format.
   * @param array $country
   *   (optional) List of supported countries.
   *
   * @return bool
   *   Boolean representation of validation result.
   */
  public function isValid($value, $format, array $country = []) {

    try {
      // Get default country.
      $default_region = ($format == PhoneNumberFormat::NATIONAL) ? reset($country) : NULL;
      // Parse object.
      $number = $this->phone_utils->parse($value, $default_region);
    }
    catch (\Exception $e) {
      return FALSE;
    }
    // Perform basic validation.
    if (!$this->phone_utils->isValidNumber($number)) {
      return FALSE;
    }

    // If #validation_countries is not empty and default region can be loaded
    // do region matching validation.
    // This condition is always TRUE for national phone number format.
    if (!empty($country) && $default_region = $this->phone_utils->getRegionCodeForNumber($number)) {
      // If number should belong to one of selected countries.
      // This condition is always TRUE for national phone number format.
      if (array_search($default_region, $country) === FALSE) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Changes number to format.
   *
   * @param string $number
   *   Telephone number.
   * @param array $settings
   *   Settings array.
   *
   * @return string
   *   Phone number in new format.
   */
  public function storeFormat($number, array $settings) {
    // Get default country.
    $default_region = ($settings['valid_format'] === PhoneNumberFormat::NATIONAL) ? reset($settings['valid_countries']) : NULL;
    // Parse object.
    $number_object = $this->phone_utils->parse($number, $default_region);
    // Change phone number format to $settings['store_format'].
    return $this->phone_utils->format($number_object, $settings['store_format']);
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