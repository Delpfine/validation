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

use Fuel\Validation\Exception\InvalidField;
use Fuel\Validation\Exception\InvalidRule;

/**
 * Main entry point for the validation functionality. Handles registering validation rules and loading validation
 * adaptors.
 *
 * @package Fuel\Validation
 * @author  Fuel Development Team
 * @since   2.0
 */
class Validator
{

	/**
	 * Contains a list of fields and all their rules
	 *
	 * @var RuleInterface[][]
	 */
	protected $rules = array();

	/**
	 * Keeps track of the last field added for magic method chaining
	 *
	 * @var string
	 */
	protected $lastAddedField;

	/**
	 * Adds a rule that can be used to validate a field
	 *
	 * @param string        $field
	 * @param RuleInterface $rule
	 *
	 * @return $this
	 *
	 * @since 2.0
	 */
	public function addRule($field, RuleInterface $rule)
	{
		if ( ! array_key_exists($field, $this->rules))
		{
			$this->addField($field);
		}

		$this->rules[$field][] = $rule;

		return $this;
	}

	/**
	 * Adds a new field to the validation object
	 *
	 * @param string $field
	 *
	 * @return $this
	 *
	 * @since 2.0
	 */
	public function addField($field)
	{
		$this->rules[$field] = array();
		$this->lastAddedField = $field;

		return $this;
	}

	/**
	 * Returns a list of all known validation rules for a given field.
	 *
	 * @param string $field Name of the field to get rules for, or null for all fields
	 *
	 * @throws InvalidField
	 *
	 * @return RuleInterface[]|RuleInterface[][]
	 *
	 * @since 2.0
	 */
	public function getRules($field = null)
	{
		// Check if we are fetching a specific field or all
		if ( ! is_null($field))
		{
			// Now we know we have a field check that we know about it
			if (array_key_exists($field, $this->rules))
			{
				// It's a known field so grab the rules for it
				$results = $this->rules[$field];
			}
			// If not throw an exception
			else
			{
				throw new InvalidField($field);
			}
		}
		else
		{
			// No field was specified so return all the fields' rules
			$results = $this->rules;
		}

		return $results;
	}

	/**
	 * Takes an array of data and validates that against the assigned rules.
	 * The array is expected to have keys named after fields.
	 * This function will call reset() before it runs.
	 *
	 * @param array $data
	 *
	 * @return ResultInterface
	 *
	 * @since 2.0
	 */
	public function run(array $data, ResultInterface $result = null)
	{
		if (is_null($result))
		{
			$result = new Result;
		}

		$result->setResult(true);

		foreach ($data as $fieldName => $value)
		{
			$fieldResult = $this->validateField($fieldName, $value, $data, $result);

			if ( ! $fieldResult)
			{
				// There was a failure so log it to the result object
				$result->setResult(false);
			}
		}

		return $result;
	}

	/**
	 * Validates a single field
	 *
	 * @param string          $field
	 * @param mixed           $value
	 * @param mixed[]       & $data
	 * @param ResultInterface $resultInterface
	 *
	 * @return bool
	 *
	 * @since 2.0
	 */
	protected function validateField($field, $value, &$data, ResultInterface $resultInterface)
	{
		$rules = $this->getRules($field);

		$result = true;

		foreach ($rules as $rule)
		{
			$result = $rule->validate($value, $field, $data);

			if ( ! $result)
			{
				// Don't allow any others to run if this one failed
				$resultInterface->setError($field, $rule->getMessage());
				break;
			}
		}

		if ($result)
		{
			// All is good so make sure the field gets added as one of the validated fields
			$resultInterface->setValidated($field);
		}

		return $result;
	}

	/**
	 * Allows validation rules to be dynamically added using method chaining.
	 *
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @return $this
	 * @throws InvalidRule
	 *
	 * @since 2.0
	 */
	public function __call($name, $arguments)
	{
		// Create and then add the new rule to the last added field
		$rule = $this->createRuleInstance($name, $arguments);

		$this->addRule($this->lastAddedField, $rule);

		return $this;
	}

	/**
	 * Creates an instance of the given rule name
	 *
	 * @param string $name
	 * @param mixed  $parameters
	 *
	 * @return RuleInterface
	 *
	 * @since 2.0
	 */
	public function createRuleInstance($name, $parameters = null)
	{
		$className = $this->getRuleClassName($name);
		return new $className($parameters);
	}

	/**
	 * Returns the full class name for the given validation rule
	 *
	 * @param $name
	 *
	 * @return string
	 * @throws InvalidRule
	 *
	 * @since 2.0
	 */
	protected function getRuleClassName($name)
	{
		// Convert the function name into a rule class
		$className = '\Fuel\Validation\Rule\\' . ucfirst($name);

		// If the class does not exist throw an error
		if ( ! class_exists($className))
		{
			throw new InvalidRule($name);
		}

		return $className;
	}

}
