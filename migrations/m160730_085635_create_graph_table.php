<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation for table `graph`.
 */
class m160730_085635_create_graph_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('graphs', [
            'id' => Schema::TYPE_PK,
            'graphname' =>  Schema::TYPE_STRING . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        $this->addForeignKey("graphs_to_user_id", "graphs", "user_id", "users", "id");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey("graphs_to_user_id", "graphs");
        $this->dropTable('graphs');
    }
}
