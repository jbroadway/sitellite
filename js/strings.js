/**
 * The following is a collection of helper functions for string objects.
 *
 */

/**
 * Convert newlines to br tags.
 *
 */
String.prototype.nl2br = function () {
	return this.replace (/\n/g, "<br />");
}

/**
 * Convert br tags to newlines.
 *
 */
String.prototype.br2nl = function () {
	return this.replace (/\<br ?\/?\>/g, "\n");
}
