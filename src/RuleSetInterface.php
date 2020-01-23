<?php

namespace Gabrieljmj\LaravelRuleSets;

/**
 * @author Gabriel Jacinto <gamjj74@hotmail.com>
 */
interface RuleSetInterface
{
    /**
     * Returns the rules of the set.
     *
     * @return array
     */
    public function getRules(): array;

    /**
     * Combines current rule set with another.
     *
     * @param array $rules
     *
     * @return \Gabrieljmj\LaravelRuleSets\RuleSetInterface
     */
    public function combineWithRules(array $rules): RuleSetInterface;
}
