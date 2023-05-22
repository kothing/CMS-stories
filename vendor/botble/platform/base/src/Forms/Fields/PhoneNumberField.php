<?php

namespace Botble\Base\Forms\Fields;

use Assets;
use Kris\LaravelFormBuilder\Fields\FormField;

class PhoneNumberField extends FormField
{
    protected function getTemplate(): string
    {
        Assets::addStylesDirectly('vendor/core/core/base/libraries/intl-tel-input/css/intlTelInput.min.css')
            ->addScriptsDirectly([
                'vendor/core/core/base/libraries/intl-tel-input/js/intlTelInput.min.js',
                'vendor/core/core/base/js/phone-number-field.js',
            ]);

        return 'core/base::forms.fields.phone-number';
    }
}
