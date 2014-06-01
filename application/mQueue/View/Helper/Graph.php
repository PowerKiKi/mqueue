<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;
use \mQueue\Model\User;

class Graph extends Zend_View_Helper_Abstract
{

    /**
     * Returns a graph for everybody or single user
     * @param \mQueue\Model\User $user
     * @return string
     */
    public function graph(\mQueue\Model\User $user = null)
    {

        $params = array('controller' => 'status', 'action' => 'graph');
        if ($user) {
            $params['user'] = $user->id;
        }
        $url = $this->view->serverUrl() . $this->view->url($params, 'default');

        $js = <<<STRING
        $(document).ready(function() {
            var refreshGraph = function() {
                var percentage = $('#graph_percent').is(':checked') ? '?percent=1' : '';
                $.get('$url' + percentage, function (chart) {
                    chart = $.parseJSON(chart);
                    $('#chart_container').highcharts(chart);
                });
            };

            $('#graph_percent').change(refreshGraph);
            refreshGraph();

        });
STRING;

        $html = '<div id="chart_container"  style="min-height: 400px"></div>
                <input type="checkbox" name="graph_percent" id="graph_percent" value="1">
                <label for="graph_percent">Show graph as stacked percentage</label>';

        $this->view->headScript()
                ->appendFile('/js/min/highcharts.js')
                ->appendScript($js);

        return $html;
    }

}
