<?php

namespace Botble\Menu\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MenuRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:120',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
