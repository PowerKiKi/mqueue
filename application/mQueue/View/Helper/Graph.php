<?php

namespace mQueue\View\Helper;

use mQueue\Model\User;
use Zend_View_Helper_Abstract;

class Graph extends Zend_View_Helper_Abstract
{
    /**
     * Returns a graph for everybody or single user
     *
     * @param User $user
     *
     * @return string
     */
    public function graph(User $user = null)
    {
        $params = ['controller' => 'status', 'action' => 'graph'];
        if ($user) {
            $params['user'] = $user->id;
        }
        $url = $this->view->serverUrl() . $this->view->url($params, 'default');

        $js = <<<STRING
        $(document).ready(function() {
            const refreshGraph = function() {
                const percentage = $('#graph_percent').is(':checked') ? '?percent=1' : '';
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
