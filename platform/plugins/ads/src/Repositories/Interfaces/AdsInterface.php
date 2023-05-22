<?php

namespace Botble\Ads\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface AdsInterface extends RepositoryInterface
{
    public function getAll(): Collection;
}
