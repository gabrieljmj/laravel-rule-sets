<?php

namespace Gabrieljmj\LaravelRuleSets;

use InvalidArgumentException;

/**
 * @author Gabriel Jacinto <gamjj74@hotmail.com>
 */
abstract class AbstractRuleSet implements RuleSetInterface
{
    /**
     * Configured rules.
     *
     * @var array
     */
    private $injectedRules = null;

    /**
     * Rules set to combines with.
     * The order of the combination will be:
     * add the combination rules than this object rules.
     *
     * @var array
     */
    protected $combineWith = [];

    /**
     * Override rules of combinations with rules of this set.
     *
     * @var bool
     */
    protected $overrideRules = false;

    /**
     * Returns all the rules combined with the rule sets indicated.
     *
     * @return array
     */
    public function getRules(): array
    {
        if (null !== $this->injectedRules) {
            return $this->injectedRules;
        }

        $rules = [];
        $selfRules = $this->rules();

        foreach ($this->combineWith as $i => $ruleSet) {
            if (!($ruleSet instanceof RuleSetInterface)) {
                throw new \DomainException('Rule set of index \'' . $i . '\' is not a valid \Gabrieljmj\LaravelRuleSets\RuleSetInterface instance.');
            }

            $rules = array_merge($rules, $ruleSet->getRules());
        }

        foreach ($selfRules as $field => $rule) {
            if (!isset($rules[$field]) || $this->overrideRules) {
                $rules[$field] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Combine this rules with anothe ones.
     *
     * @param array $rules
     *
     * @return \Gabrieljmj\LaravelRuleSets\RuleSetInterface
     */
    public function combineWithRules(array $rules): RuleSetInterface
    {
        $oldRules = $this->injectedRules ? $this->injectedRules : $this->getRules();
        $this->injectedRules = array_merge($oldRules, ...$this->normalizeRules($rules));

        return $this;
    }

    /**
     * Returns set rules.
     *
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * Returns rules of rule sets arrays or objects as array.
     *
     * @param array $rules
     *
     * @return array
     */
    private function normalizeRules(array $rules): array
    {
        return array_map(function ($ruleSet) {
            if ($this->validateRuleSet($ruleSet)) {
                return $ruleSet instanceof RuleSetInterface ? $ruleSet->getRules() : $ruleSet;
            }
        }, $rules);
    }

    /**
     * Checks if the set is an instance of RuleSet or an array.
     *
     * @param \App\Rules\RuleSets\RuleSet|array $rules
     * @param mixed                             $ruleSet
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    private function validateRuleSet($ruleSet): bool
    {
        if (!($ruleSet instanceof RuleSetInterface) && !is_array($ruleSet)) {
            throw new \DomainException('Rules must be instance of \Gabrieljmj\LaravelRuleSets\RuleSetInterface or array.');
        }

        return true;
    }
}
