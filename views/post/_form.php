<?php

use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Post;

/** @var yii\web\View $this */
/** @var app\models\Post $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $userList */


$currentUser = Yii::$app->user->identity;
?>

<div class="post-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]); ?>

    <?= $form->field($model, 'author_id')->dropDownList(
        ArrayHelper::map($userList, 'id', 'username')
    );  ?>

    <?= $form->field($model, 'status')->dropDownList(
        Post::getStatusOptions()
    ); ?>

    <!--    --><?php
    //= $form->field($model, 'created_at')->textInput() ?>

    <!--    --><?php
    //= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
