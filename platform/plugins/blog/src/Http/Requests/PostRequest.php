<?php

namespace Botble\Blog\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Supports\PostFormat;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PostRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'name' => 'required|max:255',
            'description' => 'max:400',
            'categories' => 'required',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];

        $postFormats = PostFormat::getPostFormats(true);

        if (count($postFormats) > 1) {
            $rules['format_type'] = Rule::in(array_keys($postFormats));
        }

        return $rules;
    }
}
