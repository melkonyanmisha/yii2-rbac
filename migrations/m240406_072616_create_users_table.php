<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m240406_072616_create_users_table extends Migration
{
    /**
     * @return void
     */
    public function safeUp():void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'auth_key' => $this->string(32),
            'access_token' => $this->string()->unique(),
            'role' => $this->string()->notNull()->defaultValue('user'), // Default role is 'user'
            'status' => $this->smallInteger()->notNull()->defaultValue(10), // Default status is 'active'
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

    }

    /**
     * @return void
     */
    public function safeDown():void
    {
        $this->dropTable('{{%users}}');
    }
}
