// ==UserScript==
// @name           mQueue - Injects toolbar to rate IMDB movies
// @namespace      mQueue
// @include        *
// @exclude        http://movies.lucki.ch*
// @exclude        http://mqueue*
// ==/UserScript==


// Configure the mQueue server to use
var server = "http://mqueue/"



// Inject script if any links to IMDB found on the current page
if (document.evaluate('count(//a[contains(@href, "title/tt")])', document, null, XPathResult.NUMBER_TYPE, null).numberValue > 0)
{
    var script = document.createElement('script');
    script.src = server + "js/remote.js.php";
    document.getElementsByTagName('head')[0].appendChild(script);
}
