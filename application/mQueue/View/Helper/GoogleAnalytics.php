<?php

namespace mQueue\View\Helper;

use Zend_Application;
use Zend_View_Helper_Abstract;

class GoogleAnalytics extends Zend_View_Helper_Abstract
{
    /**
     * Returns javascript code for Google Analytics.
     *
     * @param string $trackingCode
     *
     * @return string
     */
    public function googleAnalytics($trackingCode = null)
    {
        if (!is_string($trackingCode)) {
            global $application;
            if ($application instanceof Zend_Application) {
                $trackingCode = $application->getOption('googleAnalyticsTrackingCode');
            }
        }

        $trackingCode = trim($trackingCode);
        if (!is_string($trackingCode) || empty($trackingCode)) {
            return '';
        }

        $result = <<<STRING
<script async src="https://www.googletagmanager.com/gtag/js?id=$trackingCode"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '$trackingCode');
</script>
STRING;

        return $result;
    }
}
