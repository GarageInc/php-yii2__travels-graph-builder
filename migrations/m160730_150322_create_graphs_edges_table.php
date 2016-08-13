<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation for table `graphs_edges`.
 */
class m160730_150322_create_graphs_edges_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('edges', [
            'id' => Schema::TYPE_PK,
            'node_first_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'node_second_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'graph_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'weight' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        $this->addForeignKey("edges_to_f_node","edges","node_first_id", "nodes","id");
        $this->addForeignKey("edges_to_s_node","edges","node_second_id", "nodes","id");
        $this->addForeignKey("edges_to_graph_id","edges","graph_id", "graphs","id");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey("edges_to_graph_id", "edges");
        $this->dropForeignKey("edges_to_s_node", "edges");
        $this->dropForeignKey("edges_to_f_node", "edges");

        $this->dropTable('edges');
    }
}
