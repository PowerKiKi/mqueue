/*global $, document */

var mqueue = (function() {

    /**
     * JSONp function to set and retrieve new status
     * @return
     */
    function setStatus()
    {
        var parent = $(this).parent();
        parent.addClass('loading');
        $.getJSON(this.href + '?format=json&jsoncallback=?', function(data)
        {
            $('.mqueue_status_links_' + data.id).each(function()
            {
                $(this).replaceWith(data.status);
            });

            parent.removeClass('loading');
            $('.mqueue_status_links_' + data.id + ' .mqueue_status').click(setStatus);
        });
        return false;
    }

    /**
     * Bind ajax on all status links
     * @return
     */
    //var bindStatus = function ()
    function bindStatus()
    {
        $('a.mqueue_status').click(setStatus);
    }

    /**
     * Scan the current page for IMDB links and inject appropriate icons
     * @param server
     * @param node
     * @return
     */
    function injectStatus(server, node)
    {
        var maxPerQuery = 400;
        var list = [];
        var queries = [];
        var query = '';
        var i = 0;

        // Find every references to any movies, within the node, or the node itself
        var selector = "a[href*='title/tt'], link[rel='canonical']";
        var matches = $(selector, node);
        if ($(node).is(selector))
            matches.push(node[0]);

        $.each(matches, function()
        {
            var regexp = /imdb\.(com|de|es|fr|it|pt)\/title\/tt(\d{7})/;
            if (regexp.test(this.href))
            {
                var array = regexp.exec(this.href);
                var id = array[2];
                if (list[id] === undefined)
                {
                    list[id] = [];
                    query += id + ',';
                }
                list[id].push(this);

                if (i++ > maxPerQuery)
                {
                    queries.push(query);
                    query = '';
                    i = 0;
                }
            }
        });

        if (i !== 0)
        {
            queries.push(query);
        }

        if (queries.length === 0)
        {
            return;
        }

        // Get statuses from server per bunch of queries
        $.each(queries, function(x, query)
        {
            var url = server + "status/list/movies/" + query + "?format=json&jsoncallback=?";
            $.getJSON(url, function(data)
            {
                $.each(data.status, function(id, status)
                {
                    // Add status beside main title if on the main page of movie
                    if ($("link[rel='canonical'][href*='" + id.split('_')[0] + "']").length !== 0)
                    {
                        $("#tn15title > h1 .pro-link", node).before(status); // Old IMDb version (www.imdb.fr)
                        $(".article h1.header", node).append(status); // New IMDb version, next to title
                        $(".star-box.giga-star .rating.rating-list", node).replaceWith(status); // New IMDb version, replace IMDb rating
                        $(".wlb_classic_wrapper", node).html(status); // New IMDb version, replace watch list button

                    }
                    // Add status on every links concerning that movie
                    else
                    {
                        var selector = "a[href*='/title/tt" + id.split('_')[0] + "']";
                        $(selector, node).after(status);
                        $(node).filter(selector).after(status);
                    }
                });

                bindStatus();
            });
        });
    }

    /**
     * Monitor any links refering to IMDb, existing now or in future
     * @param server
     **/
    function monitorLinks(server)
    {
        var timeoutId;
        var insertedNodes = [];

        injectStatus(server, $(document));
        $('body').bind('DOMNodeInserted', function(e) {
            insertedNodes.push(e.target);

            // Cancel previous timeout if any
            if (typeof timeoutId == "number") {
                window.clearTimeout(timeoutId);
                delete timeoutId;
            }

            // Set short timeout to actually injectStatus (several nodes at once)
            timeoutId = window.setTimeout(function() {

                // Filter inserted nodes to keep only nodes without status links (to avoid duplicated injection)
                var nodesWitoutStatus = [];
                $.each(insertedNodes, function(x, node) {

                    var selector = ".mqueue_status_links";
                    var statusExist = $(e.target).is(selector) || $(selector, e.target).length;
                    if (!statusExist) {
                        nodesWitoutStatus.push(node);
                    }
                });
                insertedNodes = [];

                injectStatus(server, nodesWitoutStatus);
            }, 300);
        });
    }

    /**
     * Public API
     */
    return {
        bindStatus: bindStatus,
        monitorLinks: monitorLinks
    };

}());

$(document).ready(function()
{
    mqueue.bindStatus();
});
