<?php
/**
 * @author mistergonza <gonza.work@gmail.com>
 */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ForbiddenController extends AbstractActionController
{
    public function indexForbidden()
    {
        $response = $this->getResponse();
        $response->setStatusCode(403);

        return new ViewModel();
    }
}