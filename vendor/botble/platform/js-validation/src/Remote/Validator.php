<?php

namespace Botble\JsValidation\Remote;

use Botble\JsValidation\Support\AccessProtectedTrait;
use Botble\JsValidation\Support\RuleListTrait;
use Illuminate\Contracts\Validation\Validator as BaseValidator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\ValidationRuleParser;

class Validator
{
    use AccessProtectedTrait;
    use RuleListTrait;

    /**
     * Validator extension name.
     */
    public const EXTENSION_NAME = 'js_validation';

    protected BaseValidator $validator;

    protected bool $escape;

    public function __construct(BaseValidator $validator, bool $escape = false)
    {
        $this->validator = $validator;
        $this->escape = $escape;
    }

    public function validate(string $field, array $parameters = []): void
    {
        $attribute = $this->parseAttributeName($field);
        $validationParams = $this->parseParameters($parameters);
        $validationResult = $this->validateJsRemoteRequest($attribute, $validationParams);

        $this->throwValidationException($validationResult, $this->validator);
    }

    protected function parseAttributeName($data): int|string|null
    {
        parse_str($data, $attrParts);
        $attrParts = is_null($attrParts) ? [] : $attrParts;
        $newAttr = array_keys(Arr::dot($attrParts));

        return array_pop($newAttr);
    }

    /**
     *  Parse Validation parameters.
     *
     * @param array $parameters
     * @return array
     */
    protected function parseParameters($parameters)
    {
        $newParams = ['validate_all' => false];
        if (isset($parameters[0])) {
            $newParams['validate_all'] = $parameters[0] === 'true';
        }

        return $newParams;
    }

    /**
     * Validate remote Javascript Validations.
     *
     * @param string $attribute
     * @param array $parameters
     * @return array|bool
     */
    protected function validateJsRemoteRequest($attribute, $parameters)
    {
        $this->setRemoteValidation($attribute, $parameters['validate_all']);

        $validator = $this->validator;
        if ($validator->passes()) {
            return true;
        }

        $messages = $validator->messages()->get($attribute);

        if ($this->escape) {
            foreach ($messages as $key => $value) {
                $messages[$key] = e($value);
            }
        }

        return $messages;
    }

    /**
     * Sets data for validate remote rules.
     *
     * @param string $attribute
     * @param bool $validateAll
     * @return void
     */
    protected function setRemoteValidation($attribute, $validateAll = false)
    {
        $validator = $this->validator;
        $rules = $validator->getRules();
        $rules = $rules[$attribute] ?? [];

        if (in_array('no_js_validation', $rules)) {
            $validator->setRules([$attribute => []]);

            return;
        }

        if (! $validateAll) {
            $rules = $this->purgeNonRemoteRules($rules, $validator);
        }

        $validator->setRules([$attribute => $rules]);
    }

    /**
     * Remove rules that should not be validated remotely.
     *
     * @param array $rules
     * @param BaseValidator $validator
     * @return mixed
     */
    protected function purgeNonRemoteRules($rules, $validator)
    {
        $this->createProtectedCaller($validator);

        foreach ($rules as $i => $rule) {
            $parsedRule = ValidationRuleParser::parse([$rule]);
            if (! $this->isRemoteRule($parsedRule[0])) {
                unset($rules[$i]);
            }
        }

        return $rules;
    }

    /**
     * Throw the failed validation exception.
     *
     * @param bool $result
     * @param BaseValidator $validator
     * @return void
     *
     * @throws ValidationException|HttpResponseException
     */
    protected function throwValidationException($result, $validator)
    {
        $response = new JsonResponse($result, 200);

        if ($result !== true && class_exists(ValidationException::class)) {
            throw new ValidationException($validator, $response);
        }

        throw new HttpResponseException($response);
    }
}
