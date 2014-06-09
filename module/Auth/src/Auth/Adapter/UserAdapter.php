<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Model;

use 
Zend\Authentication\AuthenticationService,
Zend\Authentication\Storage\Session as SessionStorage,
Zend\Authentication\Adapter\DbTable as AuthAdapter;

class UserAdapter
{
    private $authAdapter;
    private $authService;
    private $storage;
    
    public function __construct($db_adapter)
    {
        $this->authAdapter = new AuthAdapter($db_adapter);
        $this->authAdapter
                ->setTableName('users')
                ->setIdentityColumn('login')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('MD5(?)');
        
        $this->authService =  new AuthenticationService();
        $this->storage = new SessionStorage();
        
        
    }
    
    public function authenticate()
    {
        
    }
    

}
