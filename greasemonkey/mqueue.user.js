// ==UserScript==
// @name           MQueue - Add to movie list for movie link
// @namespace      imdb
// @include        http://www.imdb.com/
// ==/UserScript==



// Les messages sont pr?ormatt? (breakline ?la place de \n)
var style=".mqueue {padding: 0px; margin: 0px; width: 80px; height: 16px; border: none; overflow: hidden;}";
var head=document.getElementsByTagName("HEAD")[0];
var el=window.document.createElement('link');
el.rel='stylesheet';
el.type='text/css';
el.href='data:text/css;charset=utf-8,'+escape(style);
head.appendChild(el);



function insertAfter(node, referenceNode)
{
	referenceNode.parentNode.insertBefore(node, referenceNode.nextSibling);
}
function insertBefore(node, referenceNode)
{
	referenceNode.parentNode.insertBefore(node, referenceNode);
}

var allLinks, thisLink;
allLinks = document.evaluate(
	'//a[contains(@href, "title/tt")]',
	document,
	null,
	XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE,
	null);

for (var i = 0; i < allLinks.snapshotLength; i++) 
{
	thisLink = allLinks.snapshotItem(i);
	
	regexp = /www\.imdb\.com\/title\/tt(\d{7})\/$/;
	if (regexp.test(thisLink.href))
	{
		var array = regexp.exec(thisLink.href);

		var link = document.createElement("iframe");
		link.setAttribute("src", "http://mqueue/status/index/movie/" + array[1]);
		link.appendChild(document.createTextNode("no iframe text"));
		insertBefore(link, thisLink);      
		link.setAttribute("class", 'mqueue');

	}
}
