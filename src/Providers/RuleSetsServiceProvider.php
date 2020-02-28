<?php

namespace Gabrieljmj\LaravelRuleSets\Providers;

use Gabrieljmj\LaravelRuleSets\Console\Command\MakeRuleSet;
use Illuminate\Support\ServiceProvider;

class RuleSetsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRuleSet::class,
            ]);
        }
    }
}
