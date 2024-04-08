<?php

namespace nsusoft\validators;

use nsusoft\validators\assets\InnValidationAsset;
use Yii;
use yii\helpers\Json;
use yii\validators\Validator;

class InnValidator extends Validator
{
    const INN_INDIVIDUAL_LENGTH = 12;
    const INN_LEGAL_LENGTH = 10;

    const INN_INDIVIDUAL_CHECKSUM_LENGTH = 2;
    const INN_LEGAL_CHECKSUM_LENGTH = 1;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if (null === $this->message) {
            $this->message = Yii::t('app', '"{attribute}" must be a valid INN.');
        }
    }

    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute): void
    {
        $inn = (string)$model->$attribute;

        if (!preg_match('/^(\d{10}|\d{12})$/', $inn)) {
            $this->addError($model, $attribute, $this->message);
            return;
        }

        $multipliers = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];

        if (self::INN_INDIVIDUAL_LENGTH === strlen($inn) && !$this->validateIndividual($inn, $multipliers)) {
            $this->addError($model, $attribute, $this->message);
        } else if (self::INN_LEGAL_LENGTH === strlen($inn) && !$this->validateLegal($inn, $multipliers)) {
            $this->addError($model, $attribute, $this->message);
        }
    }

    /**
     * @param string $inn
     * @param array $multipliers
     * @return bool
     */
    private function validateIndividual(string $inn, array $multipliers): bool
    {
        $innNumberLength = self::INN_INDIVIDUAL_LENGTH - self::INN_INDIVIDUAL_CHECKSUM_LENGTH;
        $checkDigit1 = $this->calculateCheckDigit($inn, $innNumberLength, $multipliers);

        if ($checkDigit1 != substr($inn, -2, 1)) {
            return false;
        }

        $firstMultiplier = 3;
        array_unshift($multipliers, $firstMultiplier);
        $checkDigit2 = $this->calculateCheckDigit($inn, $innNumberLength + 1, $multipliers);

        return $checkDigit2 == substr($inn, -1, 1);
    }

    /**
     * @param string $inn
     * @param array $multipliers
     * @return bool
     */
    private function validateLegal(string $inn, array $multipliers): bool
    {
        array_shift($multipliers);
        $checkDigit = $this->calculateCheckDigit($inn, self::INN_LEGAL_LENGTH - self::INN_LEGAL_CHECKSUM_LENGTH, $multipliers);

        return $checkDigit == substr($inn, -1, 1);
    }

    /**
     * @param string $inn
     * @param int $length
     * @param array $multipliers
     * @return int
     */
    private function calculateCheckDigit(string $inn, int $length, array $multipliers): int
    {
        $sum = 0;

        for ($i = 0; $i < $length; $i++) {
            $sum += substr($inn, $i, 1) * $multipliers[$i];
        }

        return $sum % 11 % 10;
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view): string
    {
        InnValidationAsset::register($view);
        $options = $this->getClientOptions($model, $attribute);

        return 'yii.validation.inn(value, messages, ' . Json::htmlEncode($options) . ');';
    }

    /**
     * @inheritDoc
     */
    public function getClientOptions($model, $attribute): array
    {
        $label = $model->getAttributeLabel($attribute);

        $options = [
            'message' => $this->formatMessage($this->message, ['attribute' => $label]),
        ];

        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return $options;
    }
}