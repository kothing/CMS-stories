<?php

namespace Botble\Language\Models;

use Botble\Base\Models\BaseModel;
use Botble\Setting\Models\Setting;
use Botble\Widget\Models\Widget;
use Exception;
use Theme;

class Language extends BaseModel
{
    public $timestamps = false;

    protected $primaryKey = 'lang_id';

    protected $table = 'languages';

    protected $fillable = [
        'lang_name',
        'lang_locale',
        'lang_is_default',
        'lang_code',
        'lang_is_rtl',
        'lang_flag',
        'lang_order',
    ];

    protected static function boot()
    {
        parent::boot();

        self::deleted(function (Language $language) {
            $defaultLanguage = self::where('lang_is_default', 1)->first();

            if (empty($defaultLanguage) && self::exists()) {
                $defaultLanguage = self::first();
                $defaultLanguage->lang_is_default = 1;
                $defaultLanguage->save();
            }

            $meta = LanguageMeta::where('lang_meta_code', $language->lang_code)->get();

            try {
                foreach ($meta as $item) {
                    $item->reference()->delete();
                }
            } catch (Exception $exception) {
                info($exception->getMessage());
            }

            LanguageMeta::where('lang_meta_code', $language->lang_code)->delete();

            $themeNameByLanguage = Theme::getThemeName() . '-' . $language->lang_code;

            Setting::where('key', 'LIKE', 'theme-' . $themeNameByLanguage . '-%')->delete();
            Widget::where('theme', 'LIKE', $themeNameByLanguage)->delete();
        });
    }
}
