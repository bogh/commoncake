<?php

class CommonUser extends CommonAppModel {

    public $validate = array(
        'username' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'The username is required.'
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'The username is already taken.'
            )
        ),
        'old_password' => array(
            'rule' => 'validateOldPassword',
            'message' => 'Incorrect old password.'
        ),
        'password' => array(
            'minLength' => array(
                'rule' => array('minLength', 8),
                'message' => 'The password must be at least 8 characters long.'
            )
        ),
        'confirm_password' => array(
            'rule' => 'validateConfirmPassword',
            'message' => 'Password doesn\'t match.'
        ),
    );

    public function beforeSave($options = array()) {
        $this->__password();

        return true;
    }

    private function __password() {
        if (isset($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
    }

    public function validateOldPassword($value) {
        return $this->field('password') == AuthComponent::password(array_pop($value));
    }

    public function validateConfirmPassword() {
        return $this->data[$this->alias]['password'] === $this->data[$this->alias]['confirm_password'];
    }

}
