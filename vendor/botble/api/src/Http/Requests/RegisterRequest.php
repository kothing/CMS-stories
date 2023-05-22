<?php

namespace Botble\Api\Http\Requests;

use ApiHelper;
use Botble\Support\Http\Requests\Request;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:120|min:2',
            'last_name' => 'required|max:120|min:2',
            'email' => 'required|max:60|min:6|email|unique:' . ApiHelper::getTable(),
            'password' => 'required|min:6|confirmed',
        ];
    }
}
