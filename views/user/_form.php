<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="users-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true, 'readonly' => ! $model->isNewRecord]); ?>

<!--    --><?php //= $form->field($model, 'auth_key')->textInput(['maxlength' => true]); ?>

<!--    --><?php //= $form->field($model, 'access_token')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'role')->dropDownList(['administrator' => 'Administrator', 'moderator' => 'Moderator', 'user' => 'User']); ?>

    <?= $form->field($model, 'status')->textInput(); ?>


<!--    --><?php //= $form->field($model, 'created_at')->textInput(); ?>

<!--    --><?php //= $form->field($model, 'updated_at')->textInput(); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']); ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
