<?php

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
   * Phone Number util.
   *
   * @var \libphonenumber\PhoneNumberUtil
   */
  public $phoneUtils;

  /**
   * Country Manager service.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  public $countryManager;

  /**
   * Validator constructor.
   */
  public function __construct(CountryManagerInterface $country_manager) {
    $this->phoneUtils = PhoneNumberUtil::getInstance();
    $this->countryManager = $country_manager;
  }

  /**
   * Check if number is valid for given settings.
   *
   * @param string $value
   *   Phone number.
   * @param int $format
   *   Supported input format.
   * @param array $regions
   *   (optional) List of supported countries. If empty all countries are valid.
   * @param null|string $default_region
   *   (optional) Default country
   *
   * @return bool
   *   Boolean representation of validation result.
   */
  public function isValid($value, $format, array $regions = [], $default_region = NULL) {

    try {
      // Get default region.
      // If default region is not provided and national format in use,
      // we must assume default region.
      if (!isset($default_region) && $format == PhoneNumberFormat::NATIONAL) {
        $default_region = reset($regions);
      }
      // Parse to object.
      $number = $this->phoneUtils->parse($value, $default_region);
    }
    catch (\Exception $e) {
      // If number could not be parsed by phone utils that's a one good reason
      // to say it's not valid.
      return FALSE;
    }
    // Perform basic telephone validation.
    if (!$this->phoneUtils->isValidNumber($number)) {
      return FALSE;
    }

    // If country array is not empty and default country can be loaded
    // do region matching validation.
    // This condition is always TRUE for national phone number format.
    if (!empty($regions) && (empty($default_region) || $regions = array_merge($regions, [$default_region => $default_region])) && $default_region = $this->phoneUtils->getRegionCodeForNumber($number)) {
      // Check if number's region matches list of supported countries.
      if (array_search($default_region, $regions) === FALSE) {
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
    $regions = [];
    foreach ($this->countryManager->getList() as $region => $name) {
      $region_meta = $this->phoneUtils->getMetadataForRegion($region);
      if (is_object($region_meta)) {
        $regions[$region] = (string) new FormattableMarkup('@country - +@country_code', [
          '@country' => $name,
          '@country_code' => $region_meta->getCountryCode(),
        ]);
      }
    }
    return $regions;
  }

}
