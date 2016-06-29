<?php
/**
 * @package   Fuel\Validation
 * @version   2.0
 * @author    Fuel Development Team
 * @license   MIT License
 * @copyright 2010 - 2013 Fuel Development Team
 * @link      http://fuelphp.com
 */

namespace Fuel\Validation;

/**
 * Defines a common interface for validation results
 *
 * @package Fuel\Validation
 * @author  Fuel Development Team
 *
 * @since   2.0
 */
interface ResultInterface
{

	/**
	 * Sets a true false flag for if the validation result is successful or not.
	 *
	 * @param bool $isValid
	 *
	 * @return $this
	 *
	 * @since 2.0
	 */
	public function setResult($isValid);

	/**
	 * True if the validation passed
	 *
	 * @return bool
	 *
	 * @since 2.0
	 */
	public function isValid();

	/**
	 * Gets an error message for the given field. Will return null if there is no message.
	 *
	 * @param string $field
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public function getError($field);

	/**
	 * Returns a list of all validation errors.
	 *
	 * @return string[]
	 *
	 * @since 2.0
	 */
	public function getErrors();

	/**
	 * Sets the message for a given field.
	 *
	 * @param string $field
	 * @param string $message
	 * @param string $rule
	 *
	 * @return $this
	 *
	 * @since 2.0
	 */
	public function setError($field, $message, $rule);

	/**
	 * Returns a list of fields that where successfully validated.
	 *
	 * @return string[]
	 *
	 * @since 2.0
	 */
	public function getValidated();

	/**
	 * Sets the name of a field that has passed validation
	 *
	 * @param string $field
	 *
	 * @return $this
	 *
	 * @since 2.0
	 */
	public function setValidated($field);


	/**
	 * Returns a list of rules that caused fields to fail, indexed by the field name.
	 *
	 * @return RuleInterface[]
	 *
	 * @since 2.0
	 */
	public function getFailedRules();

	/**
	 * Merges the result from the given interface to this result.
	 *
	 * @param ResultInterface $resultInterface
	 * @param string          $fieldPrefix     Prefix to be added to any field names.
	 *
	 * @return $this
	 */
	public function merge(ResultInterface $resultInterface, $fieldPrefix = '');

}
