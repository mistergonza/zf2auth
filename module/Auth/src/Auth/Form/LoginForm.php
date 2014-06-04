<?php
/**
 * Login Form
 * 
 * @author a.ludvikov <gonza.work@gmail.com>
 */
namespace Auth\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
   public function __construct($name = null)
    {
        parent::__construct('login');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'login',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Login',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'OK',
                'id' => 'submitbutton',
            ),
        ));
    }

}
