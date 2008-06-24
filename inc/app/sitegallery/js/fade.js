// cross-browser fade-out, fade-in, and fade-between effects

var fade_o = 100;
var fade_i = 0;
var fade_d = 0;
var fade_agent = navigator.userAgent.toLowerCase();
var fade_moz = ((fade_agent.indexOf ('gecko') != -1) && (fade_agent.indexOf ('khtml') == -1));;

function _fade_out (id) {
	e = document.getElementById (id);

	if (fade_o > 0) {
		e.style.filter = 'alpha(opacity=' + fade_o + ')';
		e.style.mozOpacity = fade_o / 100;
		e.style.opacity = fade_o / 100;
		fade_o -= 10;
		setTimeout ('_fade_out (\'' + id + '\')', fade_d)
	} else if (fade_o <= 0) {
		e.style.filter = 'alpha(opacity=' + 0 + ')';
		e.style.mozOpacity = 0;
		e.style.opacity = 0;
		e.style.visibility = 'hidden';
	}

	return false;
}

function fade_out (id) {
	fade_o = 100;
	setTimeout ('_fade_out (\'' + id + '\')', fade_d);
	return false;
}

function _fade_in (id) {
	e = document.getElementById (id);

	if (fade_i == 0) {
		e.style.filter = 'alpha(opacity=' + fade_i + ')';
		e.style.mozOpacity = 0;
		e.style.opacity = 0;
		fade_i += 10;
		setTimeout ('_fade_in (\'' + id + '\')', fade_d);
	} else if (fade_i <= 100) {
		e.style.filter = 'alpha(opacity=' + fade_i + ')';
		e.style.mozOpacity = fade_i / 100;
		e.style.opacity = fade_i / 100;
		fade_i += 10;
		setTimeout ('_fade_in (\'' + id + '\')', fade_d);
	}

	return false;
}

function fade_in (id) {
	fade_i = 0;
	fade (id);
	document.getElementById (id).style.visibility = 'visible';
	setTimeout ('_fade_in (\'' + id + '\')', fade_d);
	return false;
}

function fade (id) {
	e = document.getElementById (id);
	e.style.filter = 'alpha(opacity=' + 0 + ')';
	e.style.mozOpacity = 0;
	e.style.opacity = 0;
	return false;
}

function fade_between (out_id, in_id) {
	fade_out (out_id);
	fade_in (in_id);
	return false;
}
