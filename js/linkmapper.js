// adds class="external" to links pointing to other websites
// and class="file" and target="_blank" to file links.
// these can then be styled as needed.
//
// usage: simply include the script and it will do the rest for you.
//
// <script language="javascript" src="/js/linkmapper.js"> </script>
//

function linkmapper () {
	elements = document.getElementsByTagName ('a');
	for (i = 0; i < elements.length; i++) {
		host = new RegExp ('^http:\/\/' + location.hostname.replace ('.', '\\.') + '\/', 'i');
		if (elements[i].href.match (/^http:/) && ! elements[i].href.match (host)) {
			elements[i].className += ' external';
		}
		if (elements[i].href.match (/\/cms-filesystem-action/)) {
			elements[i].className += ' file';
			elements[i].setAttribute ('target', '_blank');
		}
	}
}

if (window.addEventListener) {
	window.addEventListener ('load', linkmapper, false);
} else if (window.attachEvent) {
	var r = window.attachEvent ('onload', linkmapper);
}
