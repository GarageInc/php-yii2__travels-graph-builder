<?php

use yii\db\Migration;
use yii\db\Schema;

class m160730_081036_create_user_table extends Migration
{
    public function safeUp()
    {
//        $tableOptions = null;
//        if ($this->db->driverName === 'mysql') {
//            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
//        }

        $this->createTable('users', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'password' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . ' NOT NULL',
            'token' => Schema::TYPE_STRING . ' NOT NULL',
        ]);

        //$this->execute($this->addUserSql());
    }

    private function addUserSql()
    {
        $password = Yii::$app->security->generatePasswordHash('admin');
        $auth_key = Yii::$app->security->generateRandomString();
        $token = Yii::$app->security->generateRandomString() . '_' . time();

        return "INSERT INTO users (username, email, password, auth_key, token) VALUES (admin, admin@graphs.loc, ".$password.", ".$auth_key.", ".$token.")";
    }

    public function safeDown()
    {
        $this->dropTable('users');
    }
}
