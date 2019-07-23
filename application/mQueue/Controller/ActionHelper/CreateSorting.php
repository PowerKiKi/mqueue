<?php

namespace mQueue\Controller\ActionHelper;

use Zend_Controller_Action_Helper_Abstract;

class CreateSorting extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Initialize the view for sorting based on $sortParameterName and store/retrieve values from session
     * It will look for parameter "{name}Key" and "{name}Order" in request.
     * It will add variables to the view:
     *    - sortParameterName
     *  - sortSelectedKey
     *  - sortSelectedOrder
     *
     * @param string $sortParameterName used to look for parameters in request and as key in $_SESSION
     * @param array $allowedKeys
     *
     * @return string similar to 'name DESC'
     */
    public function createSorting($sortParameterName, array $allowedKeys)
    {
        $sortOrderParameterName = $sortParameterName . 'Order';
        $key = $this->getRequest()->getParam($sortParameterName);
        $order = $this->getRequest()->getParam($sortOrderParameterName);

        $view = $this->getActionController()->view;
        $view->sortParameterName = $sortParameterName;
        $view->sortSelectedKey = $key;
        $view->sortSelectedOrder = $order;

        if (is_array($view->permanentParams)) {
            $view->permanentParams[$sortParameterName] = $key;
            $view->permanentParams[$sortOrderParameterName] = $order;
        }

        return self::getSorting($key, $order, $allowedKeys);
    }

    /**
     * Returns a valid SQL sorting snippet
     *
     * @param string $key
     * @param string $order
     * @param array $allowedKeys
     *
     * @return string similar to 'name DESC'
     */
    private static function getSorting($key, $order, array $allowedKeys)
    {
        if (!in_array($key, $allowedKeys)) {
            $key = reset($allowedKeys);
        }

        $order = strcasecmp($order, 'desc') == 0 ? 'DESC' : 'ASC';

        return $key . ' ' . $order;
    }

    /**
     * Strategy pattern: call helper as broker method
     *
     * @param string $sortParameterName used to look for parameters in request and as key in $_SESSION
     * @param array $allowedKeys
     *
     * @return string similar to 'name DESC'
     */
    public function direct($sortParameterName, array $allowedKeys)
    {
        return $this->createSorting($sortParameterName, $allowedKeys);
    }
}
