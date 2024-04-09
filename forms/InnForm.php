<?php

namespace app\forms;

use nsusoft\validators\InnValidator;
use Yii;
use yii\base\Model;

class InnForm extends Model
{
    /**
     * @var string|null
     */
    public ?string $inn = null;

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
     * @inheritDoc
     */
    public function attributeLabels(): array
    {
        return [
            'inn' => Yii::t('app', "INN"),
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

        return true;
    }
}