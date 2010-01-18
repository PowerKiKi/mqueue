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

function scanIMDB()
{
	var query = '';
	var list = new Array();
	$("a[href*='title/tt']").each(function()
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
	
	var server = 'http://mqueue/';
	var url = server + "status/list/movies/" + query + "?format=json&jsoncallback=?";
	$.getJSON(url, function(data)
	{
		$.each(data.status, function(id, status)
		{
			//alert(id);
			//alert(status);
			q = "a[href*='/title/tt" + id + "']";
			
			$(q).before(status);
		});
		
		scanStatus();
	});
}

$(document).ready(function()
{
	scanStatus();
})
