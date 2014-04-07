<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;
use Zend_Application;

class GoogleAnalytics extends Zend_View_Helper_Abstract
{

    /**
     * Returns javascript code for Google Analytics
     * @param string $trackingCode
     * @return string
     */
    public function googleAnalytics($trackingCode = null)
    {
        if (!is_string($trackingCode)) {
            global $application;
            if ($application instanceof Zend_Application) {
                $trackingCode = $application->getOption('googleAnalyticsTrackingCode', null);
            }
        }

        $trackingCode = trim($trackingCode);
        if (!is_string($trackingCode) || empty($trackingCode)) {
            return '';
        }

        $result = <<<STRING
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$trackingCode']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
STRING;

        return $result;
    }

}
