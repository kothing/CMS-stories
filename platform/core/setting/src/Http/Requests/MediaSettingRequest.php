<?php

namespace Botble\Setting\Http\Requests;

use Botble\Support\Http\Requests\Request;

class MediaSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'media_aws_access_key_id' => 'required_if:media_driver,s3',
            'media_aws_secret_key' => 'required_if:media_driver,s3',
            'media_aws_default_region' => 'required_if:media_driver,s3',
            'media_aws_bucket' => 'required_if:media_driver,s3',
            'media_aws_url' => 'required_if:media_driver,s3',
        ];
    }
}
