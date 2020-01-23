<?php

use Gabrieljmj\LaravelRuleSets\AbstractRuleSet;
use Gabrieljmj\LaravelRuleSets\RuleSetInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Gabrieljmj\LaravelRuleSets\AbstractRuleSet
 */
class RuleSetTest extends TestCase
{
    /**
     * @covers ::getRules
     */
    public function testReturnOfRules()
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ];
        $ruleset = $this->getMockForRuleSet($rules);

        $this->assertEquals($rules, $ruleset->getRules());
    }

    /**
     * @covers ::getRules
     */
    public function testReturnOfRulesWhenCombiningWithRulesSets()
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ];
        $toCombineWithRules = [
            'name' => 'required|string'
        ];
        $expected = array_merge($rules, $toCombineWithRules);

        $toCombineWith = $this->getMockForRuleSet($toCombineWithRules);
        $ruleset = $this->getMockForRuleSet($rules, [$toCombineWith]);

        $this->assertEquals($expected, $ruleset->getRules());
    }

    /**
     * @covers ::getRules
     */
    public function testCombiningRulesWithOverrideEnabled()
    {
        $rules = [
            'password' => 'required|min:8|confirmed',
        ];
        $toCombineWithRules = [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ];
        $expected = [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ];

        $toCombineWith = $this->getMockForRuleSet($toCombineWithRules);
        $ruleset = $this->getMockForRuleSet($rules, [$toCombineWith], true);

        $this->assertEquals($expected, $ruleset->getRules());
    }

    /**
     * @covers ::getRules
     */
    public function testCombiningRulesWithOverrideDisabled()
    {
        $rules = [
            'password' => 'required|min:8|confirmed',
        ];
        $toCombineWithRules = [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ];
        $expected = [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ];

        $toCombineWith = $this->getMockForRuleSet($toCombineWithRules);
        $ruleset = $this->getMockForRuleSet($rules, [$toCombineWith], false);

        $this->assertEquals($expected, $ruleset->getRules());
    }

    /**
     * @covers ::combineWithRules
     */
    public function testCombineWithRulesShouldReturnsMergedRules()
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ];
        $toCombineWithRules = [
            'name' => 'required|string'
        ];
        $anotherRuleSet = [
            'birthday' => 'required|date'
        ];
        $expected = array_merge($rules, $toCombineWithRules, $anotherRuleSet);

        $toCombineWith = $this->getMockForRuleSet($toCombineWithRules);
        $ruleset = $this->getMockForRuleSet($rules);
        $ruleset->combineWithRules([$toCombineWith, $anotherRuleSet]);

        $this->assertEquals($expected, $ruleset->getRules());
    }

    /**
     * @covers ::getRules
     *
     * @throws \DomainException
     */
    public function testInvalidRuleSetOnCombineWithPropertyShouldThrowDomainException()
    {
        $this->expectException(\DomainException::class);

        $combineWith = [
            new \stdClass,
        ];
        $ruleset = $this->getMockForRuleSet([], [$combineWith]);

        $ruleset->getRules();
    }

    /**
     * @covers ::combineWithRule
     *
     * @throws \DomainException
     */
    public function testInvalidParametersOnCombineWithRulesMethodShouldThrowsDomainException()
    {
        $this->expectException(\DomainException::class);

        $combineWith = [
            new \stdClass,
        ];
        $ruleset = $this->getMockForRuleSet([]);
        $ruleset->combineWithRules($combineWith);
    }

    /**
     * @param array     $rules
     * @param array     $combineWith
     * @param bool|null $override
     *
     * @return \Gabrieljmj\LaravelRuleSets\RuleSetInterface
     */
    private function getMockForRuleSet(array $rules, ?array $combineWith = [], bool $override = null)
    {
        $stub = $this->getMockForAbstractClass(AbstractRuleSet::class);
        $reflection = new ReflectionClass($stub);

        $stub
            ->expects($this->any())
            ->method('rules')
            ->will($this->returnValue($rules))
        ;

        if (null !== $combineWith) {
            $reflectionOverrideRulesProperty = $reflection->getProperty('combineWith');
            $reflectionOverrideRulesProperty->setAccessible(true);

            $reflectionOverrideRulesProperty->setValue($stub, $combineWith);
        }

        if (null !== $override) {
            $reflectionOverrideRulesProperty = $reflection->getProperty('overrideRules');
            $reflectionOverrideRulesProperty->setAccessible(true);

            $reflectionOverrideRulesProperty->setValue($stub, $override);
        }

        return $stub;
    }
}
