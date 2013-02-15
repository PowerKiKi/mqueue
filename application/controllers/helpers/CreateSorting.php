<?php

class OKpilot_Controller_ActionHelper_CreateSorting extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Initialize the view for sorting based on $sortParameterName and store/retrive values from session
     * It will look for parameter "{name}Key" and "{name}Order" in request.
     * It will add variables to the view:
     * 	- sortParameterName
     *  - sortSelectedKey
     *  - sortSelectedOrder
     * @param  string $sortParameterName       used to look for parameters in request and as key in $_SESSION
     * @param  array  $allowedKeys
     * @param  string $transformColumnCallback
     * @return string similar to 'name DESC'
     */
    public function createSorting($sortParameterName, array $allowedKeys, $transformColumnCallback = null)
    {
        $view = $this->getActionController()->view;
        $view->sortParameterName = $sortParameterName;
        if ($this->getRequest()->getParam($sortParameterName.'Key')) {
            $view->sortSelectedKey = $this->getRequest()->getParam($sortParameterName.'Key');
            $_SESSION[$sortParameterName.'Key'] = $view->sortSelectedKey;
        } else {
            $view->sortSelectedKey = @$_SESSION[$sortParameterName.'Key'];
        }

        if ($this->getRequest()->getParam($sortParameterName.'Order')) {
            $view->sortSelectedOrder = $this->getRequest()->getParam($sortParameterName.'Order');
            $_SESSION[$sortParameterName.'Order'] = $view->sortSelectedOrder;
        } else {
            $view->sortSelectedOrder = @$_SESSION[$sortParameterName.'Order'];
        }

        $key = $view->sortSelectedKey;
        $order = $view->sortSelectedOrder;

        return self::getSorting($key, $order, $allowedKeys, $transformColumnCallback);
    }

    /**
     * Returns a valid SQL sorting snippet
     * @param  string $key
     * @param  string $order
     * @param  array  $allowedKeys
     * @param  string $transformColumnCallback
     * @return string similar to 'name DESC'
     */
    private static function getSorting($key, $order, array $allowedKeys, $transformColumnCallback = null)
    {
        if (!in_array($key, $allowedKeys)) {
            $key = reset($allowedKeys);
        }

        // If callback is given use it ton transform key to real column name in database
        if ($transformColumnCallback) {
            $key = call_user_func($transformColumnCallback, $key);
        }

        $order = strcasecmp($order, 'desc') == 0 ? 'DESC' : 'ASC';

        return $key . ' ' . $order;
    }

    /**
     * Returns the sorting stored in session
     * @param  string $sortParameterName
     * @param  array  $allowedKeys
     * @param  string $transformColumnCallback
     * @return string similar to 'name DESC'
     */
    public static function getSortingFromSession($sortParameterName, array $allowedKeys, $transformColumnCallback = null)
    {
        $key = @$_SESSION[$sortParameterName . 'Key'];
        $order = @$_SESSION[$sortParameterName . 'Order'];

        return self::getSorting($key, $order, $allowedKeys, $transformColumnCallback);
    }

    /**
     * Strategy pattern: call helper as broker method
     * @param  string $sortParameterName used to look for parameters in request and as key in $_SESSION
     * @return null
     */
    public function direct($sortParameterName, array $allowedKeys, $transformColumnCallback = null)
    {
        return $this->createSorting($sortParameterName, $allowedKeys, $transformColumnCallback);
    }
}
