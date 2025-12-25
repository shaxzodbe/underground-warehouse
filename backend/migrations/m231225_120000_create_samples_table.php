<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%samples}}`.
 */
class m231225_120000_create_samples_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%samples}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'type' => "ENUM('normal', 'cooling') NOT NULL",
            'status' => "ENUM('stored', 'held', 'dropped', 'expired') DEFAULT 'stored'",
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'expires_at' => $this->timestamp()->null(), // For cooling type
            'x' => $this->integer()->defaultValue(null),
            'y' => $this->integer()->defaultValue(null),
        ]);

        $this->createIndex(
            '{{%idx-samples-status}}',
            '{{%samples}}',
            'status'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%samples}}');
    }
}
