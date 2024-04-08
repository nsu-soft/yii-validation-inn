<?php

namespace nsusoft\validators\assets;

use yii\validators\ValidationAsset;
use yii\web\AssetBundle;

class InnValidationAsset extends AssetBundle
{
    public $sourcePath = __dir__ . '/source';

    public $js = [
        'js/inn.validation.js',
    ];

    public $depends = [
        ValidationAsset::class,
    ];
}