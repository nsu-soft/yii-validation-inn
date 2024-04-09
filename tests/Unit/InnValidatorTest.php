<?php


namespace Tests\Unit;

use app\forms\InnForm;
use Codeception\Test\Unit;
use nsusoft\validators\InnValidator;
use Tests\Support\UnitTester;
use Yii;
use yii\validators\Validator;

class InnValidatorTest extends Unit
{
    const CORRECT_INN_INDIVIDUAL = '012437782038';
    const CORRECT_INN_LEGAL = '0815687402';
    const INCORRECT_INN_INDIVIDUAL = '012437782039';
    const INCORRECT_INN_LEGAL = '0815687403';
    const SHORT_INN = '081568740';
    const LONG_INN = '0124377820380';
    const INCORRECT_INN_TEXT = 'abc';

    protected UnitTester $tester;

    protected function _before()
    {
    }

    public function testCorrectIndividualInn(): void
    {
        $validator = new InnValidator();
        $this->assertTrue($validator->validate(self::CORRECT_INN_INDIVIDUAL));
    }

    public function testCorrectLegalInn(): void
    {
        $validator = new InnValidator();
        $this->assertTrue($validator->validate(self::CORRECT_INN_LEGAL));
    }

    public function testIncorrectDigitIndividualInn(): void
    {
        $validator = new InnValidator();
        $this->assertFalse($validator->validate(self::INCORRECT_INN_INDIVIDUAL));
    }

    public function testIncorrectDigitLegalInn(): void
    {
        $validator = new InnValidator();
        $this->assertFalse($validator->validate(self::INCORRECT_INN_LEGAL));
    }

    public function testShortInn(): void
    {
        $validator = new InnValidator();
        $this->assertFalse($validator->validate(self::SHORT_INN));
    }

    public function testLongInn(): void
    {
        $validator = new InnValidator();
        $this->assertFalse($validator->validate(self::LONG_INN));
    }

    public function testIncorrectNoDigitInn(): void
    {
        $validator = new InnValidator();
        $this->assertFalse($validator->validate(self::INCORRECT_INN_TEXT));
    }

    public function testIndividualType(): void
    {
        $validator = new InnValidator(['type' => InnValidator::TYPE_INDIVIDUAL]);
        $this->assertTrue($validator->validate(self::CORRECT_INN_INDIVIDUAL));
    }

    public function testLegalInnWithIndividualType(): void
    {
        $validator = new InnValidator(['type' => InnValidator::TYPE_INDIVIDUAL]);
        $this->assertFalse($validator->validate(self::CORRECT_INN_LEGAL));
    }

    public function testLegalType(): void
    {
        $validator = new InnValidator(['type' => InnValidator::TYPE_LEGAL]);
        $this->assertTrue($validator->validate(self::CORRECT_INN_LEGAL));
    }

    public function testIndividualInnWithLegalType(): void
    {
        $validator = new InnValidator(['type' => InnValidator::TYPE_LEGAL]);
        $this->assertFalse($validator->validate(self::CORRECT_INN_INDIVIDUAL));
    }

    public function testErrorMessage(): void
    {
        $message = 'Incorrect INN';
        $validator = new InnValidator(['message' => $message]);
        $validator->validate(self::INCORRECT_INN_INDIVIDUAL, $error);
        $this->assertEquals($message, $error);
    }

    public function testI18nErrorMessage(): void
    {
        Yii::$app->language = 'ru-RU';
        $ruMessage = 'Значение "INN" должно быть действительным ИНН.';
        $attribute = 'inn';

        $model = new InnForm([$attribute => self::INCORRECT_INN_INDIVIDUAL]);
        $validator = Validator::createValidator(InnValidator::class, $model, $attribute);

        $validator->validateAttribute($model, $attribute);
        $this->assertEquals($ruMessage, $model->getFirstError($attribute));
    }
}
