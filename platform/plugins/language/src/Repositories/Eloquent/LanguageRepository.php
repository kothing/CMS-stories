<?php

namespace Botble\Language\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Language\Repositories\Interfaces\LanguageInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class LanguageRepository extends RepositoriesAbstract implements LanguageInterface
{
    public function getActiveLanguage(array $select = ['*']): Collection
    {
        $data = $this->model->orderBy('lang_order')->select($select)->get();
        $this->resetModel();

        return $data;
    }

    public function getDefaultLanguage(array $select = ['*']): ?Model
    {
        $data = $this->model->where('lang_is_default', 1)->select($select)->first();
        $this->resetModel();

        return $data;
    }
}
