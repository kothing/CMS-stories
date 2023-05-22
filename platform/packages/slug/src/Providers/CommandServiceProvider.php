<?php

namespace Botble\Slug\Providers;

use Botble\Slug\Commands\ChangeSlugPrefixCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ChangeSlugPrefixCommand::class,
            ]);
        }
    }
}
