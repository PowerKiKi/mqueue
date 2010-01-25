function scanStatus()
{
	$('.status').each(function()
	{
		$(this).click(function()
		{
			$.getJSON(this.href + '?format=json&jsoncallback=?', function(data)
			{
				$('.status_links_' + data.id).each(function()
				{
					$(this).replaceWith(data.status);
				})
				scanStatus();
			})		
			return false;
		})
	})
}

function scanIMDB(server)
{
	var query = '';
	var list = new Array();
	
	// Find every references to any movies
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
		}
		
	})
	
	if (query == '')
		return;
	
	// Get statuses from server
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
		
		scanStatus();
	});
}

$(document).ready(function()
{
	scanStatus();
})
