# Yii2 INN validation

The russian individual taxpayer number (INN) validation for Yii2 framework.

## Installation

If you don't have Composer, you may install it by following instructions at [getcomposer.org](https://getcomposer.org/doc/00-intro.md).

Then you can install this library using the following command:

```bash
composer require nsu-soft/yii-validation-inn
```

## Usage

Validate INN:

```php
<?php

namespace app\forms;

use nsusoft\validators\InnValidator;
use yii\base\Model;

class InnForm extends Model
{
    public string $inn;
    
    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['inn'], InnValidator::class],
        ];
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        
        // other form logic
        
        return true; 
    }
}
```

You may specify a type of INN which you want to validate. It may be individual or legal number.

Use this rule to validate individual INN:

```php
[['inn'], InnValidator::class, 'type' => InnValidator::TYPE_INDIVIDUAL]
```

Or to validate legal INN:

```php
[['inn'], InnValidator::class, 'type' => InnValidator::TYPE_LEGAL]
```

`InnValidator::TYPE_ANY` is used in default.