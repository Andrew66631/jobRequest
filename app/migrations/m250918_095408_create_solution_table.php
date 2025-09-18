<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%solution}}`.
 */
class m250918_095408_create_solution_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%solution}}', [
            'id' => $this->primaryKey(),
            'description' => $this->text()->notNull(),
        ]);

        $this->batchInsert('{{%solution}}', ['description'], [
            ['approved'],
            ['declined'],
        ]);
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropTable('{{%solution}}');
    }
}
