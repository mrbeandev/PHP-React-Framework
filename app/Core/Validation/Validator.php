<?php

namespace App\Core\Validation;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        $validated = [];

        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $valuePresent = array_key_exists($field, $data);
            $value = $data[$field] ?? null;

            $isRequired = in_array('required', $ruleList, true);

            if (!$valuePresent) {
                if ($isRequired) {
                    $errors[$field][] = 'The field is required.';
                }
                continue;
            }

            foreach ($ruleList as $rule) {
                [$name, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

                if ($name === 'required') {
                    if ($value === null || (is_string($value) && trim($value) === '')) {
                        $errors[$field][] = 'The field is required.';
                    }
                    continue;
                }

                if ($value === null) {
                    continue;
                }

                if ($name === 'string' && !is_string($value)) {
                    $errors[$field][] = 'The field must be a string.';
                    continue;
                }

                if ($name === 'boolean' && !is_bool($value)) {
                    $errors[$field][] = 'The field must be a boolean.';
                    continue;
                }

                if ($name === 'max' && is_string($value) && $parameter !== null && strlen($value) > (int) $parameter) {
                    $errors[$field][] = "The field may not be greater than {$parameter} characters.";
                    continue;
                }

                if ($name === 'path' && (!is_string($value) || !str_starts_with($value, '/'))) {
                    $errors[$field][] = 'The field must be an absolute path starting with "/".';
                }
            }

            $validated[$field] = $value;
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $validated;
    }
}
