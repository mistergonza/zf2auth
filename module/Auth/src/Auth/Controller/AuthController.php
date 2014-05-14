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
        return new ViewModel();
    }
}
