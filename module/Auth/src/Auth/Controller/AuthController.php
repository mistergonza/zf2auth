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
        $form = new \Auth\Form\LoginForm();
        $request = $this->request;
        if($request->isPost())
        {
            $form->setData($request->getPost());
        }
        return new ViewModel(array(
            'login_form' => $form
        ));
    }
    public function logoutAction()
    {
        
    }
}
