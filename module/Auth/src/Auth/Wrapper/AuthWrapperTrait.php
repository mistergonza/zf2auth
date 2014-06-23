<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Wrapper;

trait AuthWrapperTrait
{
    private $authWrapper;
    
    /**
     * @return \Auth\Wrapper\AuthWrapper
     */
    public function getAuthWrapper()
    {
        return $this->authWrapper;
    }
    
    /**
     * @param \Auth\Wrapper\AuthWrapper $auth_wrapper
     * @return object
     */
    public function setAuthWrapper(AuthWrapper $auth_wrapper)
    {
        $this->authWrapper = $auth_wrapper;
        return $this;
    }
}
