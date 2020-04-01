<?php

namespace mQueue\Controller\ActionHelper;

use Zend_Controller_Action_Helper_Abstract;

class Headers extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Set appropriate headers according to content type
     *
     * @param mixed $contentType
     */
    public function headers($contentType): void
    {
        $response = $this->getActionController()->getResponse();
        $response->setHeader('Content-Type', $contentType);
        $response->setHeader('Cache-Control', 'max-age=604800');

        // This check is required when running via unit tests
        if (headers_sent()) {
            return;
        }

        header_remove('Pragma');
        header_remove('Expires');
    }

    /**
     * Strategy pattern: call helper as broker method
     *
     * @param mixed $data
     */
    public function direct($data): void
    {
        $this->headers($data);
    }
}
