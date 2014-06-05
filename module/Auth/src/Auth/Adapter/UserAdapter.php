<?php
/**
 * @author a.ludvikov <gonza.work@gmail.com>
 */
namespace Auth\Model;

use 
Zend\Authentication\AuthenticationService,
Zend\Authentication\Storage\Session as SessionStorage,
Zend\Authentication\Adapter\DbTable as AuthAdapter;

class UserModel
{
    private $authAdapter;
    private $authService;
    function __construct($db_adapter)
    {
        $this->authAdapter = new AuthAdapter($db_adapter);
        $this->authAdapter
                ->setTableName('users')
                ->setIdentityColumn('login')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('MD5(?)');
      
    }

}
