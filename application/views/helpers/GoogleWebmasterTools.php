<?php

class Default_View_Helper_GoogleWebmasterTools extends Zend_View_Helper_Abstract
{

    /**
     * Return a meta to be used for Google Webmaster Tools
     * @param string $verificationCode
     * @return string
     */
    public function googleWebmasterTools($verificationCode = null)
    {
        if (!is_string($verificationCode)) {
            global $application;
            if ($application instanceof Zend_Application) {
                $verificationCode = $application->getOption('googleWebmasterToolsVerificationCode', null);
            }
        }

        $verificationCode = trim($verificationCode);
        if (!is_string($verificationCode) || empty($verificationCode)) {
            return '';
        }

        $result = '<meta name="google-site-verification" content="' . $verificationCode . '" />';

        return $result;
    }

}
