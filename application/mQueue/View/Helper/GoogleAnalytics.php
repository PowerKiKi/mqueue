<?php

namespace mQueue\View\Helper;

use Zend_Application;
use Zend_View_Helper_Abstract;

class GoogleAnalytics extends Zend_View_Helper_Abstract
{
    /**
     * Returns javascript code for Google Analytics
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
<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '$trackingCode', 'auto');
  ga('send', 'pageview');
</script>
STRING;

        return $result;
    }
}
