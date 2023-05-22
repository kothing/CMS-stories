<?php

namespace Botble\Language\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface LanguageInterface extends RepositoryInterface
{
    public function getActiveLanguage(array $select = ['*']): Collection;

    public function getDefaultLanguage(array $select = ['*']): ?Model;
}
