<?php

namespace Botble\Language\Http\Requests;

use Botble\Support\Http\Requests\Request;

class LanguageRequest extends Request
{
    public function rules(): array
    {
        return [
            'lang_name' => 'required|max:30|min:2',
            'lang_code' => 'required|max:10|min:2',
            'lang_locale' => 'required|max:10|min:2',
            'lang_flag' => 'required',
            'lang_is_rtl' => 'required',
            'lang_order' => 'required|numeric',
        ];
    }
}
