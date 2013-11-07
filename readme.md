# FuelPHP Validation library.

[![Build Status](https://travis-ci.org/fuelphp/validation.png?branch=master)](https://travis-ci.org/fuelphp/validation)

A flexable library to validate different kinds of data.

##Simple usage

```php
<?php

use \Fuel\Validation\Validator

// Create a new validator instance to play with
$v = new Validator;

// Set up our required validation rules
$v->addField('name')
    ->required()
  ->addField('email')
    ->required()
    ->email()
  ->addField('age')
    ->number();

// Create some dummy data to validate
$data = array(
    'name' => 'John',
    'email' => 'john@doe.example',
    'age' => 32,
);

// Perform the validation
$result = $v->run($data);

var_dump($result->isValid()); // true
var_dump($result->validated()); // List of all the fields that passed validation

```

## Error messages

Messages can be retrieved from the result object after validatoin has been performed

```php
<?php

use \Fuel\Validation\Validator

// Create a new validator instance to play with
$v = new Validator;

// Set up our required validation rules
$v->addField('name')
    ->required()
  ->addField('email')
    ->required()
    ->email()
  ->addField('age')
    ->number();

// Create some dummy data to validate
$data = array(
    'email' => 'john',
    'age' => 'not a number',
);

// Perform the validation
$result = $v->run($data);

var_dump($result->isValid()); // false
var_dump($result->getValidated()); // array()

var_dump($result->getErrors()); // returns an array of all the error messages encountered
var_dump($result->getError('name')); // Returns the error message for the 'name' field
```

### Custom messages
Currently there is not a way to easily add custom validation messages but this feature is planned. At the moment you will have to manually add new rule instances to set custom messages.

## Manually adding rules and rule overriding
As well as using the default core rules it is possible to dynamically add your own rules or override existing rules.

This is done by calling the `addRule()` function on a `Validator` like so: `$v->addRule('myCustomRule', 'My\App\Rules\CustomRule')`.
If the class cannot be loaded for any reason a `InvalidRuleException` will be thrown when the rule gets used.

The `myCustomRule` rule is now avaliable for use with the `Validator` instance and can be called via the magic method syntax as well as the `createRuleInstance()` function in `Validator`.

```php
<?php

use \Fuel\Validation\Validator

// Create a new validator instance to play with
$v = new Validator;

$v->addRule('myCustomRule', 'My\App\Rules\CustomRule');

// Example of adding the new rule via magic method syntax
$v->addField('foobar')
    ->myCustomRule();
    
$instance = $v->getRuleInstance('myCustomRule');
var_dump($instance); // instance of My\App\Rules\CustomRule
```

### Overriding existing rules
It is possible to replace existing rules simply by calling `addRule()` as in the previous example and passing the name of an existing rule

```php
<?php

use \Fuel\Validation\Validator

// Create a new validator instance to play with
$v = new Validator;

$v->addRule('required', 'My\App\Rules\CustomRule');

// Example of adding the new rule via magic method syntax
$v->addField('foobar')
    ->required();
    
$instance = $v->getRuleInstance('required');
var_dump($instance); // instance of My\App\Rules\CustomRule
```
