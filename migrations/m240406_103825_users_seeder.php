<?php

use yii\db\Migration;
use app\seeds\UsersSeeder;

/**
 * Class m240406_103825_users_seeder
 */
class m240406_103825_users_seeder extends Migration
{
    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $seeder = new UsersSeeder();
        $seeder->safeUp();
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $seeder = new UsersSeeder();
        $seeder->safeDown();
    }
}
