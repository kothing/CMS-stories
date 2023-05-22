<?php

namespace Botble\Base\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use MacroableModels;
use MetaBox as MetaBoxSupport;

class BaseModel extends Eloquent
{
    public function __get($key)
    {
        if (class_exists('MacroableModels')) {
            $method = 'get' . Str::studly($key) . 'Attribute';
            if (MacroableModels::modelHasMacro(get_class($this), $method)) {
                return call_user_func([$this, $method]);
            }
        }

        return parent::__get($key);
    }

    public function metadata(): MorphMany
    {
        return $this->morphMany(MetaBox::class, 'reference')
            ->select([
                'reference_id',
                'reference_type',
                'meta_key',
                'meta_value',
            ]);
    }

    public function getMetaData(string $key, bool $single = false): array|string|null
    {
        $field = $this->metadata
            ->where('meta_key', apply_filters('stored_meta_box_key', $key, $this))
            ->first();

        if (! $field) {
            $field = $this->metadata->where('meta_key', $key)->first();
        }

        if (! $field) {
            return $single ? '' : [];
        }

        return MetaBoxSupport::getMetaData($field, $key, $single);
    }

    public function newEloquentBuilder($query): BaseQueryBuilder
    {
        return new BaseQueryBuilder($query);
    }
}
