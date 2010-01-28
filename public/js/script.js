/**
 * JSONp function to set and retrieve new status
 * @return
 */
function setStatus()
{
	$.getJSON(this.href + '?format=json&jsoncallback=?', function(data)
	{
		$('.status_links_' + data.id).each(function()
		{
			$(this).replaceWith(data.status);
		})
		
		$('.status_links_' + data.id + ' .status').click(setStatus);
	});		
	return false;
}

/**
 * 	Bind ajax on all status links
 * @return
 */
function bindStatus()
{
	$('.status').click(setStatus);
}

/**
 * Scan the current page for IMDB links and add appropriate icons
 * @param server
 * @return
 */
function scanIMDB(server)
{
	const maxPerQuery = 400;	
	var list = new Array();
	var queries = new Array();
	
	// Find every references to any movies
	var query = '';
	var i = 0;
	$("a[href*='title/tt'], link[rel='canonical']").each(function()
	{
		regexp = /www\.imdb\.com\/title\/tt(\d{7})\/$/;
		if (regexp.test(this.href))
		{
			var array = regexp.exec(this.href);
			var id = array[1];
			if (list[id] == null)
			{
				list[id] = new Array();
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
	})
	
	if (i != 0)
		queries.push(query);
	
	if (queries.length == 0)
		return;
	
	// Get statuses from server per bunch of queries
	$.each(queries, function(x, query)
	{
		var url = server + "status/list/movies/" + query + "?format=json&jsoncallback=?";
		$.getJSON(url, function(data)
		{
			$.each(data.status, function(id, status)
			{
				// Add status beside main title if on the main page of movie
				if ($("link[rel='canonical'][href*=" + id.split('_')[0] + "]").length != 0)
					$("#tn15title>h1").before(status);
				// Add status on every links concerning that movie
				else
					$("a[href*='/title/tt" + id.split('_')[0] + "']").before(status);
			});
			
			bindStatus();
		});
	});
}

$(document).ready(function()
{
	bindStatus();
});
