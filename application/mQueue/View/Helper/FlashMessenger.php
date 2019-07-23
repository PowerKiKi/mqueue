<?php

namespace mQueue\View\Helper;

use Zend_Controller_Action_Helper_FlashMessenger;
use Zend_Controller_Action_HelperBroker;
use Zend_View_Helper_Abstract;

/**
 * Noumenal PHP Library.
 *
 * PHP classes built on top of Zend Framework. (http://framework.zend.com/)
 *
 * Bug Reports: support@noumenal.co.uk
 * Questions  : https://noumenal.fogbugz.com/default.asp?noumenal
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file noumenal-new-bsd-licence.txt.
 * It is also available through the world-wide-web at this URL:
 *
 * http://noumenal.co.uk/license/new-bsd
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@noumenal.co.uk so we can send you a copy immediately.
 *
 * ATTRIBUTION
 *
 * Beyond maintaining the Copyright Notice and Licence, attribution is
 * appreciated but not required. Please attribute where appropriate by
 * linking to:
 *
 * http://noumenal.co.uk/
 *
 * @author     Carlton Gibson <carlton.gibson@noumenal.co.uk>
 * @copyright  Copyright (c) 2009 Noumenal Software Ltd. (http://noumenal.co.uk/)
 * @license    http://noumenal.co.uk/license/new-bsd     New BSD License
 *
 * @version    $Revision: 3 $ $Date: 2009-08-13 16:02:49 +0100 (Thu, 13 Aug 2009) $ modified for specific purpose
 */

/**
 * View Helper to Display Flash Messages.
 *
 * Checks for messages from previous requests and from the current request.
 *
 * Checks for `array($key => $value)` pairs in FlashMessenger's messages array.
 * If such a pair is found, $key is taken as the "message level", $value as the
 * message. (Simple strings are provided a default level of 'warning'.)
 *
 * NOTE: MESSAGES ARE PRESUMED TO BE SAFE HTML. IF REDISPLAYING USER
 * INPUT, ESCAPE ALL MESSAGES PRIOR TO ADDING TO FLASHMESSENGER.
 */
class FlashMessenger extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    private $_flashMessenger;

    /**
     * If the flashmessenger is postoned it will do nothing on first call.
     *
     * @var bool
     */
    private $isPostponed = false;

    /**
     * Initializing stuff. Provides fluid interface.
     */
    public function flashMessenger()
    {
        return $this;
    }

    /**
     * Render the flash messages
     *
     * @return string flash messages formatted as div
     */
    public function __toString()
    {
        if ($this->isPostponed) {
            $this->isPostponed = false;

            return '';
        }

        $flashMessenger = $this->_getFlashMessenger();

        //get messages from previous requests
        $messages = $flashMessenger->getMessages();

        //add any messages from this request
        if ($flashMessenger->hasCurrentMessages()) {
            $messages = array_merge(
                $messages, $flashMessenger->getCurrentMessages()
            );
            //we don't need to display them twice.
            $flashMessenger->clearCurrentMessages();
        }

        //initialise return string
        $output = '';

        //process messages
        foreach ($messages as $message) {
            $level = 'notice';
            if (is_array($message)) {
                [$level, $message] = each($message);
            }
            $output .= '<div class="flashmessenger ' . $level . '">' . $message . '</div>';
        }

        return $output;
    }

    /**
     * Postpone the flash messages to the next next call. The next call will return empty string.
     *
     * @param bool $isPostponed
     */
    public function postpone(bool $isPostponed = true): void
    {
        $this->isPostponed = $isPostponed;
    }

    /**
     * Returns the flash messenger
     *
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    protected function _getFlashMessenger()
    {
        if (null === $this->_flashMessenger) {
            $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        }

        return $this->_flashMessenger;
    }
}
