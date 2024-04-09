<?php

namespace app\seeds;

use Yii;
use Faker\Factory;
use yii\base\Exception;
use yii\db\Migration;
use app\models\User;

class UsersSeeder extends Migration
{
    /**
     * @return void
     * @throws Exception
     */
    public function safeUp(): void
    {
        $usersData = [];

        // Administrator user
        $usersData[] = [
            'admin',
            'admin@example.com',
            Yii::$app->security->generatePasswordHash('admin123'),
            Yii::$app->security->generateRandomString(),
            User::STATUS_ACTIVE,
            time(), // created_at
            time(), // updated_at
            'administrator', // role
        ];

        // Moderator user
        $usersData[] = [
            'moderator',
            'moderator@example.com',
            Yii::$app->security->generatePasswordHash('admin123'),
            Yii::$app->security->generateRandomString(),
            User::STATUS_ACTIVE,
            time(), // created_at
            time(), // updated_at
            'moderator', // role
        ];

        // Simple user
        $usersData[] = [
            'user',
            'user@example.com',
            Yii::$app->security->generatePasswordHash('admin123'),
            Yii::$app->security->generateRandomString(),
            User::STATUS_ACTIVE,
            time(), // created_at
            time(), // updated_at
            'user', // role
        ];

        // Generate data for 10 dynamic users
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $usersData[] = [
                $faker->userName,
                $faker->email,
                Yii::$app->security->generatePasswordHash($faker->password),
                Yii::$app->security->generateRandomString(),
                User::STATUS_ACTIVE,
                time(), // created_at
                time(), // updated_at
                'user', // role
            ];
        }

        // Batch insert the users
        $this->batchInsert('users', [
            'username',
            'email',
            'password_hash',
            'auth_key',
            'status',
            'created_at',
            'updated_at',
            'role',
        ], $usersData);
    }


    /**
     * @return void
     */
    public function safeDown(): void
    {
        User::deleteAll();
    }
}
