<?php

namespace Botble\Analytics\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function propertyIdNotSpecified(): static
    {
        return new static(trans('plugins/analytics::analytics.property_id_not_specified'));
    }

    public static function credentialsIsNotValid(): static
    {
        return new static(trans('plugins/analytics::analytics.credential_is_not_valid'));
    }
}
