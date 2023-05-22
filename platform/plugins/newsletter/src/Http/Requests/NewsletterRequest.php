<?php

namespace Botble\Newsletter\Http\Requests;

use Botble\Newsletter\Enums\NewsletterStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class NewsletterRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'email' => 'required|email|unique:newsletters',
            'status' => Rule::in(NewsletterStatusEnum::values()),
        ];

        if (is_plugin_active('captcha') && setting('enable_captcha')) {
            $rules += ['g-recaptcha-response' => 'required|captcha'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => __('Captcha Verification Failed!'),
            'g-recaptcha-response.captcha' => __('Captcha Verification Failed!'),
        ];
    }
}
