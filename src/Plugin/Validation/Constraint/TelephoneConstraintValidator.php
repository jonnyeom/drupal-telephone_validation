<?php

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
   * Validator service.
   *
   * @var \Drupal\telephone_validation\Validator
   */
  protected $validator;

  /**
   * {@inheritdoc}
   */
  public function initialize(ExecutionContextInterface $context) {
    $this->context = $context;
    $this->validator = \Drupal::service('telephone_validation.validator');
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
    /** @var \Drupal\field\Entity\FieldConfig $field */
    $field = $value->getFieldDefinition();
    $settings = $field->getThirdPartySettings('telephone_validation');
    // If no settings found we must skip validation.
    if (empty($settings)) {
      return;
    }
    // Validate number against validation settings.
    if (!$this->validator->isValid(
      $number['value'],
      $field->getThirdPartySetting('telephone_validation', 'format'),
      $field->getThirdPartySetting('telephone_validation', 'country')
    )) {
      $this->context->addViolation($constraint->message, ['@number' => $number['value']]);
    }
  }

}
