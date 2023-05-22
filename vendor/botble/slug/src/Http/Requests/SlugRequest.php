<?php

namespace Botble\Slug\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SlugRequest extends Request
{
    public function rules(): array
    {
        return [
            'value' => 'required',
            'slug_id' => 'required',
        ];
    }
}
