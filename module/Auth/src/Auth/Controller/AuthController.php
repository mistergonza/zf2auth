<?php
/**
 * @author a.ludvikov <gonza.work@gmail.com>
 */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

class AuthController extends AbstractActionController
{
    public function loginAction()
    {
        $request = $this->request;
        $form = new \Auth\Form\LoginForm();
        return new ViewModel(array(
            'login_form' => $form
        ));
    }
    public function logoutAction()
    {
        
    }
}
