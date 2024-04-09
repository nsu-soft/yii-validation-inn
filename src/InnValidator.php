<?php

namespace nsusoft\validators;

use nsusoft\validators\assets\InnValidationAsset;
use Yii;
use yii\helpers\Json;
use yii\validators\Validator;

class InnValidator extends Validator
{
    const TYPE_ANY = 0;
    const TYPE_INDIVIDUAL = 1;
    const TYPE_LEGAL = 2;

    /**
     * @var int An INN type which need to validate.
     */
    public int $type = self::TYPE_ANY;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();
        $this->registerTranslations();

        if (null === $this->message) {
            $this->message = InnValidator::t('main', '"{attribute}" must be a valid INN.');
        }
    }

    /**
     * @return void
     */
    public function registerTranslations(): void
    {
        Yii::$app->i18n->translations['validators/inn/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __dir__ . '/messages',
            'fileMap' => [
                'validators/inn/main' => 'main.php',
            ],
        ];
    }

    /**
     * @see Yii::t()
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string|null $language
     * @return string
     */
    public static function t(string $category, string $message, array $params = [], ?string $language = null): string
    {
        return Yii::t("validators/inn/{$category}", $message, $params, $language);
    }

    /**
     * @inheritDoc
     */
    public function validateValue($value): ?array
    {
        if (!preg_match('/^\d+$/', $value)) {
            return [$this->message, []];
        }

        if (self::TYPE_ANY === $this->type && !$this->validateIndividual($value) && !$this->validateLegal($value)) {
            return [$this->message, []];
        } else if (self::TYPE_INDIVIDUAL === $this->type && !$this->validateIndividual($value)) {
            return [$this->message, []];
        } else if (self::TYPE_LEGAL === $this->type && !$this->validateLegal($value)) {
            return [$this->message, []];
        }

        return null;
    }

    /**
     * @param string $inn
     * @return bool
     */
    private function validateIndividual(string $inn): bool
    {
        if (12 !== strlen($inn)) {
            return false;
        }

        $multipliers = $this->getMultiplies();
        $checkDigit1 = $this->calculateCheckDigit($inn, 10, $multipliers);

        if ($checkDigit1 != substr($inn, -2, 1)) {
            return false;
        }

        $firstMultiplier = 3;
        array_unshift($multipliers, $firstMultiplier);
        $checkDigit2 = $this->calculateCheckDigit($inn, 11, $multipliers);

        return $checkDigit2 == substr($inn, -1, 1);
    }

    /**
     * @param string $inn
     * @return bool
     */
    private function validateLegal(string $inn): bool
    {
        if (10 !== strlen($inn)) {
            return false;
        }

        $multipliers = $this->getMultiplies();
        array_shift($multipliers);
        $checkDigit = $this->calculateCheckDigit($inn, 9, $multipliers);

        return $checkDigit == substr($inn, -1, 1);
    }

    /**
     * @return int[]
     */
    private function getMultiplies(): array
    {
        return [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
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
            'type' => $this->type,
        ];

        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return $options;
    }
}