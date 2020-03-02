# Laravel Rule Sets

![Travis (.com)](https://img.shields.io/travis/com/gabrieljmj/laravel-rule-sets) ![Packagist](https://img.shields.io/packagist/l/gabrieljmj/laravel-rule-sets)

Avoid repeating validation rules sets. With this library it is possible to share rules between sets and reuse sets through requests.

## Installing

### Composer

Execute the following command:

```terminal
composer require gabrieljmj/laravel-rule-sets
```

#### For Laravel before 5.5

It is necessary to add the service provider to the providers list at ```config/app.php```:

```php
Gabrieljmj\LaravelRuleSets\Providers\RuleSetsServiceProvider::class,
```

## Usage

The package provides the artisan command ```make:rule-set```. It will generate a RuleSet at the namespace ```App\Rules\RuleSets```.

```terminal
artisan make:rule-set UserRuleSet
```

Add the necessary rules at the protected method ```rules``` of the set.

```php
<?php

namespace App\Rules\RuleSets;

use Gabrieljmj\LaravelRuleSets\AbstractRuleSet;

class UserRuleSet extends AbstractRuleSet
{
    protected function rules(): array
    {
        return [
            'username' => 'required',
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
