# Laravel Rule Sets

Avoid repeating rules sets through requests.

## Installing

### Composer

Execute the following command:

```terminal

composer require gabrieljmj/laravel-rule-sets

```

## Usage

Create a class that extends ```AbstractRuleSet``` (recommended to be created inside ```app/Rules/RuleSet```). It will provide you the ```getRules``` and ```combineWithRules``` methods.

There are one required methods:

- ```rules(): array```: Returns an array with the rules.

```php
<?php

namespace App\Rules\RuleSets;

use Gabrieljmj\LaravelRuleSets\AbstractRuleSet;

class UserRuleSet extends AbstractRuleSet
{
    protected function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
```

Then you can now inject into the ```rules``` method of the request.

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\RuleSets\UserRuleSet;

class UserRequest extends FormRequest
{
    // ...

    public function rules(UserRuleSet $userRules)
    {
        return $userRules->getRules();
    }
}
```

### Combinig rules sets

#### ```combineWithRules(array $rules)```

This method will be used to inject rules into the rule set object.

> **Note:** Rules with the same name will be overridden by the new ones.

```php
use App\Rules\RuleSets\UserRuleSet;
use App\Rules\RuleSets\PasswordRuleSet;

// ...

public function rules(UserRuleSet $userRules, PasswordRuleSet $passwordRuleSet)
{
    return $userRules
        ->combineWith([$passwordRuleSet])
        ->getRules();
}
```

#### ```$combineWith```

An array with sets that will be inject to the rules collection on the ```getRules``` execution.

```php
<?php

namespace App\Rules\RuleSets;

use Gabrieljmj\LaravelRuleSets\AbstractRuleSet;

class PersonRuleSet extends AbstractRuleSet
{
    protected function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
```

```php
<?php

namespace App\Rules\RuleSets;

use Gabrieljmj\LaravelRuleSets\AbstractRuleSet;

class UserRuleSet extends AbstractRuleSet
{
    public function __construct(PersonRuleSet $personRuleSet)
    {
        // Adds other rule set
        $this->combineWith[] = $personRuleSet;
    }

    protected function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
```

```php
$userRuleSet->getRules(); // ['email' => '...', 'password' => '...', 'name' => '...']
```

##### Override rules

Sometimes two sets will have the a rule for a input. The preferred will be from the combined with set. If it's necessary the ```getRules``` method to override the rules, just set protected property ```$overrideRules``` of the set to ```true```.

```php
<?php

namespace App\Rules\RuleSets;

use Gabrieljmj\LaravelRuleSets\AbstractRuleSet;

class UserRuleSet extends AbstractRuleSet
{
    protected $overrideRules = true;

    public function __construct(PersonRuleSet $personRuleSet)
    {
        $this->combineWith[] = $personRuleSet;
    }

    protected function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'name' => 'required|length:25',
        ];
    }
}
```
