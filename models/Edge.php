<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "edges".
 *
 * @property integer $id
 * @property integer $node_first_id
 * @property integer $node_second_id
 * @property integer $graph_id
 * @property integer $weight
 *
 * @property Graphs $graph
 * @property Nodes $nodeFirst
 * @property Nodes $nodeSecond
 */
class Edge extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edges';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['node_first_id', 'node_second_id', 'graph_id', 'weight'], 'required'],
            [['node_first_id', 'node_second_id', 'graph_id', 'weight'], 'integer'],
            [['graph_id'], 'exist', 'skipOnError' => true, 'targetClass' => Graph::className(), 'targetAttribute' => ['graph_id' => 'id']],
            [['node_first_id'], 'exist', 'skipOnError' => true, 'targetClass' => Node::className(), 'targetAttribute' => ['node_first_id' => 'id']],
            [['node_second_id'], 'exist', 'skipOnError' => true, 'targetClass' => Node::className(), 'targetAttribute' => ['node_second_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'node_first_id' => 'Node First ID',
            'node_second_id' => 'Node Second ID',
            'graph_id' => 'Graph ID',
            'weight' => 'Weight',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGraph()
    {
        return $this->hasOne(Graphs::className(), ['id' => 'graph_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNodeFirst()
    {
        return $this->hasOne(Nodes::className(), ['id' => 'node_first_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNodeSecond()
    {
        return $this->hasOne(Nodes::className(), ['id' => 'node_second_id']);
    }
}
