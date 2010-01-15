// ==UserScript==
// @name           MQueue - Add to movie list for movie link
// @namespace      imdb
// @include        http://www.imdb.com/
// ==/UserScript==


function insertBefore(node, referenceNode)
{
	referenceNode.parentNode.insertBefore(node, referenceNode);
}

var allLinks;
allLinks = document.evaluate(
	'//a[contains(@href, "title/tt")]',
	document,
	null,
	XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE,
	null);

for (var i = 0; i < allLinks.snapshotLength; i++) 
{
	var thisLink = allLinks.snapshotItem(i);
	
	regexp = /www\.imdb\.com\/title\/tt(\d{7})\/$/;
	if (regexp.test(thisLink.href))
	{
		var array = regexp.exec(thisLink.href);

		var iframe = document.createElement("iframe");
		iframe.setAttribute("src", "http://mqueue/status/index/movie/" + array[1]);
		iframe.appendChild(document.createTextNode("no iframe text"));
		insertBefore(iframe, thisLink);
		iframe.setAttribute("style", "padding: 0px; margin: 0px; width: 80px; height: 16px; border: none; overflow: hidden;");
	}
}
