<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    use \Auth\Wrapper\AuthWrapperTrait;
    
    public function loginAction()
    {
        $request = $this->request;
        $form = new \Auth\Form\LoginForm();
        $auth_wrapper = $this->getAuthWrapper();

        $auth_wrapper->setRequest($request);

        if($auth_wrapper->authenticate())
        {
            $this->redirect()->toRoute('home');
        }

        $form->setData($request->getPost());

        return new ViewModel(array(
            'login_form' => $form
        ));
    }
    
    public function testAction()
    {
        echo $this->getAuthWrapper()->getIdentity();
    }
    
    public function logoutAction()
    {
        $auth_wrapper = $this->getAuthWrapper();
        $auth_wrapper->clearIdentity();

        $this->redirect()->toRoute('auth');
    }
}
