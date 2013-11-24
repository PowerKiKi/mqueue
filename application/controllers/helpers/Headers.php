<?php

class Default_Controller_ActionHelper_Headers extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * Set appropriate headers according to content type
     * @param mixed $contentType
     * @return void
     */
    public function headers($contentType)
    {
        $response = $this->getActionController()->getResponse();
        $response->setHeader('Content-Type', $contentType);
        $response->setHeader('Cache-Control', 'max-age=604800');

        // This check is required when running via unit tests
        if (headers_sent())
            return;

        header_remove('Pragma');
        header_remove('Expires');
    }

    /**
     * Strategy pattern: call helper as broker method
     * @param mixed $data
     * @return void
     */
    public function direct($data)
    {
        return $this->headers($data);
    }

}
