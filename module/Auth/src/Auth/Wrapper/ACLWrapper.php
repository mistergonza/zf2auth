<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Wrapper;

use Zend\Permissions\Acl\Acl;
class ACLWrapper extends Acl
{

    /**
     * @param \Zend\Db\Adapter\Adapter $db_adapter
     * @param array $config
     */
    function __construct(array $config)
    {
        $acl_config = array();

        if(array_key_exists('acl', $config))
        {
            $acl_config = $config['acl'];
        }

        if(!array_key_exists('roles', $acl_config))
        {
            throw new \Exception('ACL must have roles');
        }
        if(!array_key_exists('permissions', $acl_config))
        {
            throw new \Exception('ACL must have permission params.');
        }

        foreach ($acl_config['roles'] as $role => $parent)
        {
            $this->addRole($role, $parent);
        }

        foreach ($acl_config['permissions'] as $controller => $roles)
        {
            $this->addResource($controller);
            if (array_key_exists('allow', $roles))
            {
                foreach ($roles['allow'] as $role => $actions)
                {
                    $this->allow($role, $controller, $actions);
                }
            }
            if (array_key_exists('deny', $roles))
            {
                foreach ($roles['deny'] as $role => $actions) {
                    $this->deny($role, $controller, $actions);
                }
            }
        }
    }
}

