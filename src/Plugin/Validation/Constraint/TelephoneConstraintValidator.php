<?php

/**
 * @file
 * Contains \Drupal\link\Plugin\Validation\Constraint\LinkExternalProtocolsConstraintValidator.
 */

namespace Drupal\telephone_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Validates the LinkExternalProtocols constraint.
 */
class TelephoneConstraintValidator implements ConstraintValidatorInterface {

  /**
   * Stores the validator's state during validation.
   *
   * @var \Symfony\Component\Validator\ExecutionContextInterface
   */
  protected $context;

  /**
   * @var
   */
  protected $validation_service;

  /**
   * {@inheritdoc}
   */
  public function initialize(ExecutionContextInterface $context) {
    $this->context = $context;
    $this->validation_service = \Drupal::service('tel.validator');
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    try {
      $number = $value->getValue();
    }
    catch (\InvalidArgumentException $e) {
      return;
    }
    $number = $number['value'];
    $settings = array();
    if (!$this->validation_service->isValid($number, $settings)) {
      $this->context->addViolation($constraint->message, array('@number' => $number));
    }
  }

}
