<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%history}}`.
 */
class m231225_120001_create_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%history}}', [
            'id' => $this->primaryKey(),
            'action' => $this->string()->notNull(),
            'details' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            '{{%idx-history-created_at}}',
            '{{%history}}',
            'created_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%history}}');
    }
}
