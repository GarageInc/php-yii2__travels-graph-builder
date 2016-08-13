<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "graphs".
 *
 * @property integer $id
 * @property string $graphname
 * @property integer $user_id
 *
 * @property Edges[] $edges
 * @property Users $user
 * @property Nodes[] $nodes
 */
class Graph extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'graphs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['graphname'], 'required'],
            [['user_id'], 'integer'],
            [['graphname'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'graphname' => 'Graphname',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEdges()
    {
//        $this->getAttribute()
        return $this->hasMany(Edge::className(), ['graph_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Node::className(), ['graph_id' => 'id']);
    }
}
