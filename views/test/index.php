<?php

use app\forms\InnForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var InnForm $model */
?>
<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'inn')->textInput() ?>

<?= Html::submitButton() ?>

<?php ActiveForm::end() ?>
