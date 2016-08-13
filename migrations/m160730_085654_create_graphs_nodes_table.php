<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation for table `graphs_node`.
 */
class m160730_085654_create_graphs_nodes_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('nodes', [
            'id' => Schema::TYPE_PK,
            'nodename' =>  Schema::TYPE_STRING . ' NOT NULL',
            'graph_id' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        $this->addForeignKey("nodes_to_graph_id", "nodes", "graph_id", "graphs", "id");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey("nodes_to_graph_id", "nodes");
        $this->dropTable('nodes');
    }
}
