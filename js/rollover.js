// the ultimate rollover script
// by lux

/* usage:

<script language="javascript1.1" src="/js/rollover.js"></script>
<script language="javascript1.1">
<!--

// preload images

preload ('about', '/pix/arrow-down.gif', '/pix/arrow-up.gif');
preload ('contact', '/pix/arrow-up.gif', '/pix/arrow-down.gif');

// example of advanced preloading:
preload ('multi', '/pix/infobox_tl.gif', '/pix/infobox_tr.gif', '/pix/infobox_br.gif', '/pix/infobox_bl.gif');

preload ('tl', '/pix/spacer.gif', '/pix/infobox_tl.gif');
preload ('tr', '/pix/spacer.gif', '/pix/infobox_tr.gif');
preload ('bl', '/pix/spacer.gif', '/pix/infobox_bl.gif');
preload ('br', '/pix/spacer.gif', '/pix/infobox_br.gif');

// -->
</script>

<p>
<a href="about.html" onmouseover="on ('about')" onmouseout="off ()"><img src="/pix/arrow-down.gif" name="about" alt="About Us" /></a>
</p>

<p>
<a href="about.html" onmouseover="on ('contact')" onmouseout="off ()"><img src="/pix/arrow-up.gif" name="contact" alt="Contact Us" /></a>
</p>

<p>
<a href="#" onmouseover="on ('about'); on ('contact')" onmouseout="off ()">Both</a>
</p>

<p>
<table border="1" cellpadding="0" cellspacing="0">
	<tr>
		<td><a href="#" onmouseover="off (); on('tr'); on('br')"><img src="/pix/spacer.gif" height="17" width="17" alt="" name="tl" /></a></td>
		<td><a href="#" onmouseover="off (); on('br'); on('bl')"><img src="/pix/spacer.gif" height="17" width="17" alt="" name="tr" /></a></td>
	</tr>
	<tr>
		<td><a href="#" onmouseover="off (); on('tl'); on('tr')"><img src="/pix/spacer.gif" height="17" width="17" alt="" name="bl" /></a></td>
		<td><a href="#" onmouseover="off (); on('bl'); on('tl')"><img src="/pix/spacer.gif" height="17" width="17" alt="" name="br" /></a></td>
	</tr>
</table>
</a>

<p>
<a href="#" onmouseover="on ('multi', 1)">H</a>
<a href="#" onmouseover="on ('multi', 2)">U</a>
<a href="#" onmouseover="on ('multi', 3)">H</a>
<a href="#" onmouseover="on ('multi', 4)">?</a>
</p>

<!-- complex example -->
<p>
<img src="/pix/infobox_tl.gif" name="multi" alt="Multi" width="20" height="20" />
</p>

*/

// requires JavaScript1.1 or greater

var browserOK = false;
var pics;

browserOK = true;
pics = new Array ();
var objCount = 0;

function preload (name) { //images is an array with flipCount-many values
	if (browserOK) {
	
		pics[objCount] = new Array (arguments.length);
		
		pics[objCount][0] = name;
		
		for (var i=1; i < (arguments.length + 1); i++) {
			pics[objCount][i] = new Image ();
			pics[objCount][i].src = arguments[i];
		}
		objCount++;
		
	}
}

function on (name, picNum) {

	if (picNum == null) picNum = 2; //default at simple rollover for ease of typing

	if (browserOK) {
		for (i = 0; i < objCount; i++) {
			if (document.images[pics[i][0]] != null) {
				if (name == pics[i][0])
					document.images[pics[i][0]].src = pics[i][picNum].src;
			}
		}
	}
}

function off () {
	if (arguments.length > 0) {
		on (arguments[0], 1); // 1 is always the original image
		return;
	}
	if (browserOK) {
		for (i = 0; i < objCount; i++) {
			if (document.images[pics[i][0]] != null)
				document.images[pics[i][0]].src = pics[i][1].src; //pics[i][1] is always the original image
		}
	}
}
