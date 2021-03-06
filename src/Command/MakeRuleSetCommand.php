<?php

namespace Gabrieljmj\LaravelRuleSets\Command;

use Illuminate\Console\GeneratorCommand;

class MakeRuleSetCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:rule-set';

    /**
     * The console command description.
     *
     * @var null|string
     */
    protected $description = '';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Rule set';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'../../../stubs/RuleSet.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Rules\RuleSets';
    }
}
