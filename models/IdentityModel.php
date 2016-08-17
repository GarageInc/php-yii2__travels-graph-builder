<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class IdentityModel extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if ( !$this->hasErrors()) {
            $user = $this->getUser();

            if ( $user && !$user->validatePassword( $this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login()
    {
        $user = $this->getUser();

        $salt = Yii::$app->security->generateRandomString(10);

        $token = "";

        if ( $this->validate()) {

            if( $user == null){

                $user = new User();

                $user->username = $this->username;
                $user->password = (Yii::$app->security->generatePasswordHash( $this->password));
            }

            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->token =  $salt. ':' . Yii::$app->security->generatePasswordHash($salt . $user->getAuthKey());

            if( $user->save()){

                Yii::$app->user->login( $user, $this->rememberMe ? 3600*24*30 : 0);

                $token = $user->token;
            }
        }

        return $token;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
