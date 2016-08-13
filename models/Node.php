<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nodes".
 *
 * @property integer $id
 * @property string $nodename
 * @property integer $graph_id
 *
 * @property Edges[] $edges
 * @property Edges[] $edges0
 * @property Graphs $graph
 */
class Node extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nodes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nodename', 'graph_id'], 'required'],
            [['graph_id'], 'integer'],
            [['nodename'], 'string', 'max' => 255],
            [['graph_id'], 'exist', 'skipOnError' => true, 'targetClass' => Graph::className(), 'targetAttribute' => ['graph_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nodename' => 'Nodename',
            'graph_id' => 'Graph ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEdgesAsFirst()
    {
        return $this->hasMany(Edge::className(), ['node_first_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEdgesAsSecond()
    {
        return $this->hasMany(Edge::className(), ['node_second_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGraph()
    {
        return $this->hasOne(Graph::className(), ['id' => 'graph_id']);
    }
}
