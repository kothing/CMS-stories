<?php

namespace Botble\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Concerns\InteractsWithInput;

/**
 * @mixin InteractsWithInput
 */
abstract class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
