<?php

namespace app\controllers;

use app\forms\InnForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class TestController extends Controller
{
    /**
     * @return Response|string
     */
    public function actionIndex(): Response|string
    {
        $model = new InnForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['success']);
        }

        return $this->render('index', ['model' => $model]);
    }

    public function actionSuccess(): string
    {
        return $this->render('success');
    }
}