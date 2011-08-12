/*global $, document */

var mqueue = (function () {
	var injectingStatus = false;

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
				injectingStatus = true;
				$.each(data.status, function(id, status)
				{
					// Add status beside main title if on the main page of movie
					if ($("link[rel='canonical'][href*=" + id.split('_')[0] + "]", node).length !== 0)
					{
						$("#tn15title>h1").before(status); // Old IMDb version
						$("div#main>div.article>h1.header").before(status); // New IMDb version
						$("div#main>div.article td#overview-top h1.header").before(status); // Newest IMDb version
						$("div#main>div.article td#overview-bottom div.add_to_watchlist").html(status); // Newest IMDb version

					}
					// Add status on every links concerning that movie
					else
					{
						var selector = "a[href*='/title/tt" + id.split('_')[0] + "']";
						$(selector, node).before(status);
						$(node).filter(selector).before(status);
					}
				});
				injectingStatus = false;

				bindStatus();
			});
		});
	}

	/**
	 * Monitor any links refering to IMDb, existing now or in future
	 **/
	function monitorLinks(server)
	{
		var timeoutId;
		var insertedNodes = [];

		injectStatus(server, $('body'));
		$('body').bind('DOMNodeInserted', function(e){
			if (!injectingStatus)
			{
				insertedNodes.push(e.target);
				if(typeof timeoutId == "number") {
					window.clearTimeout(timeoutId);
					delete timeoutId;
				}
				timeoutId = window.setTimeout(function() {
					var nodes = insertedNodes;
					insertedNodes = [];
					injectStatus(server, nodes);
				}, 300);

			}
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
