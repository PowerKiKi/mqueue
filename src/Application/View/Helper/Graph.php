<?php

namespace Application\View\Helper;

use Application\Model\User;
use Mezzio\LaminasView\UrlHelper;

class Graph
{
    public function __construct(
        private readonly HeadScript $headScript,
        private readonly UrlHelper $url,
    ) {}

    /**
     * Returns a graph for everybody or single user.
     */
    public function __invoke(?User $user = null): string
    {
        if ($user) {
            $url = ($this->url)('status.graph.user', ['user' => $user->id]);
        } else {
            $url = ($this->url)('status.graph');
        }

        $js = <<<JS
                    $(document).ready(function() {
                        const refreshGraph = function() {
                            const percentage = $('#graph_percent').is(':checked') ? '?percent=1' : '';
                            $.get('$url' + percentage, function (chart) {
                                $('#chart_container').highcharts(chart);
                            });
                        };

                        $('#graph_percent').change(refreshGraph);
                        refreshGraph();

                    });
            JS;

        $html = '<div id="chart_container"  style="min-height: 400px"></div>
                <input type="checkbox" name="graph_percent" id="graph_percent" value="1">
                <label for="graph_percent">Show graph as stacked percentage</label>';

        ($this->headScript)()
            ->appendFile('/js/min/highcharts.js')
            ->appendScript($js);

        return $html;
    }
}
