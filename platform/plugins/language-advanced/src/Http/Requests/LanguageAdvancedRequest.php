<?php

namespace Botble\LanguageAdvanced\Http\Requests;

use Botble\Support\Http\Requests\Request;

class LanguageAdvancedRequest extends Request
{
    public function rules(): array
    {
        return [
            'model' => 'required|max:255',
        ];
    }
}
