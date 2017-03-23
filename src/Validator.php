<?php

/**
 * @file
 * Telephone validation service.
 */

namespace Drupal\telephone_validation;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Locale\CountryManagerInterface;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

/**
 * Performs telephone validation.
 */
class Validator {

  /**
   * @var \libphonenumber\PhoneNumberUtil
   */
  public $phone_utils;

  /**
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  public $countryManager;

  /**
   * Validator constructor.
   */
  public function __construct(CountryManagerInterface $country_manager) {
    $this->phone_utils = PhoneNumberUtil::getInstance();
    $this->countryManager = $country_manager;
  }

  /**
   * Check if number is valid for given settings.
   *
   * @param $value
   *   Phone number.
   * @param $format
   *   Supported input format.
   * @param array $country
   *   (optional) List of supported countries. If empty all countries are valid.
   *
   * @return bool
   *   Boolean representation of validation result.
   */
  public function isValid($value, $format, array $country = []) {

    try {
      // Get default country.
      $default_region = ($format == PhoneNumberFormat::NATIONAL) ? reset($country) : NULL;
      // Parse to object.
      $number = $this->phone_utils->parse($value, $default_region);
    }
    catch (\Exception $e) {
      // If number could not be parsed by phone utils that's a one good reason
      // to say it's not valid.
      return FALSE;
    }
    // Perform basic telephone validation.
    if (!$this->phone_utils->isValidNumber($number)) {
      return FALSE;
    }

    // If country array is not empty and default region can be loaded
    // do region matching validation.
    // This condition is always TRUE for national phone number format.
    if (!empty($country) && $default_region = $this->phone_utils->getRegionCodeForNumber($number)) {
      // Check if number's region matches list of supported countries.
      if (array_search($default_region, $country) === FALSE) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Get list of countries with country code and leading digits.
   *
   * @return array
   *   Flatten array you can use it directly in select lists.
   */
  public function getCountryList() {
    $regions = array();
    foreach ($this->countryManager->getList() as $region => $name) {
      $region_meta = $this->phone_utils->getMetadataForRegion($region);
      if (is_object($region_meta)) {
        $regions[$region] = (string) new FormattableMarkup('@country - @country_code', [
          '@country' => $name,
          '@country_code' => $region_meta->getCountryCode() . $region_meta->getLeadingDigits()
        ]);
      }
    }
    return $regions;
  }
}
