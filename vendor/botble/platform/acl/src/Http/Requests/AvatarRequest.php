<?php

namespace Botble\ACL\Http\Requests;

use Botble\Support\Http\Requests\Request;
use RvMedia;

class AvatarRequest extends Request
{
    public function rules(): array
    {
        return [
            'avatar_file' => RvMedia::imageValidationRule(),
            'avatar_data' => 'required',
        ];
    }
}
