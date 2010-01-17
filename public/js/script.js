function scan()
{

	$('.status').each(function()
	{
		$(this).click(function()
		{
			$.getJSON(this.href + '?format=json',
			        function(data){
				$('.status_links_' + data.id).replaceWith(data.status);
				scan();
			})		
			return false;
		})
	})
}

$(document).ready(function()
{
	scan();
})