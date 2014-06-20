<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

class AuthController extends AbstractActionController
{
    use \Auth\Wrapper\AuthWrapperTrait;
    
    public function loginAction()
    {
        $request = $this->request;
        $form = new \Auth\Form\LoginForm();
        $auth_wrapper = $this->getAuthWrapper();

        $auth_wrapper->setRequest($request);

        if(!$auth_wrapper->authenticate())
        {
            $form->setData($request->getPost());
        }
        
        return new ViewModel(array(
            'login_form' => $form
        ));
    }
    
    public function bcryptAction()
    {
        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
        echo $bcrypt->create('123');
    }
    
    public function logoutAction()
    {
        $auth_wrapper = $this->getAuthWrapper();
        $auth_wrapper->logout();

        $this->redirect()->toRoute('auth');
    }
}
