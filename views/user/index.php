<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title                   = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Users', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            'email:email',
//            'password_hash',
//            'auth_key',
//            'access_token',
            'role',
            'status',
            [
                'attribute' => 'created_at',
                'value'     => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->created_at, 'php:Y-m-d H:i:s');
                },
            ],
            [
                'attribute' => 'updated_at',
                'value'     => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->updated_at, 'php:Y-m-d H:i:s');
                },
            ],
            [
                'class'      => ActionColumn::class,
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>


</div>
