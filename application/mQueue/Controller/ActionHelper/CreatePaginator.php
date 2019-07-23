<?php

namespace mQueue\Controller\ActionHelper;

use Zend_Controller_Action_Helper_Abstract;
use Zend_Paginator;
use Zend_Session_Namespace;

class CreatePaginator extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Create a new Zend_Paginator and configure it with GET or session variables
     *
     * @param mixed $data
     *
     * @return Zend_Paginator
     */
    public function createPaginator($data)
    {
        // Read perPage from session, or GET
        $perPage = 25;
        $session = new Zend_Session_Namespace();
        if (isset($session->perPage)) {
            $perPage = $session->perPage;
        }

        $userPerPage = $this->getRequest()->getParam('perPage');
        if ($userPerPage) {
            $perPage = $userPerPage;
            $session->perPage = $perPage;
        }

        // If we export to csv or rss, override perPage parameter
        $currentContext = $this->getActionController()->getHelper('contextSwitch')->getCurrentContext();
        switch ($currentContext) {
            case 'csv':
                $perPage = -1;

                break;
            case 'rss':
                $perPage = 200;

                break;
        }

        // Defines the paginator
        $paginator = Zend_Paginator::factory($data);
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
        $paginator->setItemCountPerPage($perPage);

        return $paginator;
    }

    /**
     * Strategy pattern: call helper as broker method
     *
     * @param mixed $data
     *
     * @return Zend_Paginator
     */
    public function direct($data)
    {
        return $this->createPaginator($data);
    }
}
