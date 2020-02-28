<?php

namespace Gabrieljmj\LaravelRuleSets\Providers;

use Gabrieljmj\LaravelRuleSets\Console\Command\MakeRuleSetCommand;
use Illuminate\Support\ServiceProvider;

class RuleSetsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRuleSetCommand::class,
            ]);
        }
    }
}
