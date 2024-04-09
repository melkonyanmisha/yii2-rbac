<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%posts}}`.
 */
class m240406_072928_create_posts_table extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%posts}}', [
            'id'         => $this->primaryKey(),
            'title'      => $this->string()->notNull(),
            'content'    => $this->text()->notNull(),
            'author_id'  => $this->integer()->notNull(),
            'status'     => $this->string()->notNull()->defaultValue('pending'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // Foreign key for author_id
        $this->addForeignKey(
            'fk-posts-author_id',
            'posts',
            'author_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        // Drop foreign key for author_id
        $this->dropForeignKey('fk-posts-author_id', 'posts');

        $this->dropTable('{{%posts}}');
    }
}
