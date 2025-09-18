<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request}}`.
 */
class m250918_095428_create_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'term' => $this->integer()->notNull(),
            'solution_id' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Внешние ключи
        $this->addForeignKey(
            'fk-request-user_id',
            '{{%request}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-request-solution_id',
            '{{%request}}',
            'solution_id',
            '{{%solution}}',
            'id',
            'SET NULL'
        );

        $this->createIndex('idx-request-user_id', '{{%request}}', 'user_id');
        $this->createIndex('idx-request-solution_id', '{{%request}}', 'solution_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-request-user_id', '{{%request}}');
        $this->dropForeignKey('fk-request-solution_id', '{{%request}}');
        $this->dropTable('{{%request}}');
    }
}
