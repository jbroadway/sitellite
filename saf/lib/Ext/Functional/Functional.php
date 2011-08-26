<?php

/*
	The "Functional PHP Extension" is a libarary for using a functional
	style in PHP. See http://functional-php.sourceforge.net
	
Copyright (C) 2001 Ian B Kjos

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

	Contact me at brooke@users.sourceforge.net
	Read the whole license at http://www.gnu.org/copyleft/lesser.html

Portions of this file have been donated by other parties.
The current list is:
	DB: Dave Benjamin: dave@ovumdesign.com

*/

/*
	There are many advantages to a functional style of programming.
	
	You can get a lot more expressed in a lot less verbiage, or some
	would say. In any case, functional programming (all but) eliminates
	the practice of side effects, which are a common source of odd
	behaviour (bugs).
	
	Therefore, this module attempts to provide some FP primatives
	that php either lacks or does uncomfortably.
	
	Be aware that this is a strict functional approach. That means that
	things are computed when they are declared, not when they are used. 
	It also means we tend to trade memory (and sometimes CPU) for
	programmer productivity.  Most of the time, this is not an issue.
	
	The approach I've taken is to treat PHP arrays as the fundamental
	unit of repetition. Said arrays have a number of handy properties. 
	For instance, the order in which you put things in is the order you
	get them out, regardless of the key. This makes list processing
	reliable without having to switch between hashes and arrays as in
	perl.
	
	On the other hand, there are some functions specifically geared
	towards dealing with the "hash" personality of arrays.
*/

/////////////////////////// ENOUGH TALK ////////////////////////////////

/*
	First problem: We need lambda-style functions. There will surely be
	lots of them.
	
	PHP4 almost handles this inately, but with a really long name
	('create_function()') and it accepts a procedure body instead of an
	expression the way you expect of a proper lambda operator.
	
	Lambda-style functions occur way too frequently to be hard to read. 
	Thus, the FP synonym lam, short for lambda and far easier to type. 
	(You try typing lambda 10 times fast! lam lam lam!)
	
	(The real reason for "lam" is because I keep getting tied up on the
	b and d.)
	
	You could argue for a procedural eqivalent to the lambda function. I
	am not yet convinced of the need. If it gets written, it will be
	called "lamP()" and have the main difference of not supplying the
	return() construct.
	
	There are features of PHP4 that try to make this more efficient. 
	Only trouble is that in the version my hosting company has, said
	features do have (very subtle) bugs. Therefore and since the php3
	version will work on php4 just fine, I'm going to make it the only
	version that (normally) runs.
	
	Beware, because if you do not have a perfect lambda function, you
	will get really wierd bugs. If the behavior changes when you 
	define DANGEROUS_LAMBDA, then don't do that.
	
*/

if (( floor(phpversion()) >= 4) and (defined("DANGEROUS_LAMBDA"))) {
	
	function lam($args, $expr) {
		return create_function($args, "return $expr;");
	}

} else if ( floor(phpversion()) >= 3) {
	
	function lam($args, $expr) {
		static $counter;
		$counter ++;
		$name = "__lambda_$counter";
		$code = "function $name ($args) {return $expr;}";
		eval($code);
		return $name;
	}

} else {
	die("FPE: Functional PHP Extension is not able to recognize your version of PHP.");
}


/*
	There's the occasional desire for (of all things) the identity
	function. Here it is, in all its triviality.
*/
function id($x) {
	return $x;
}
/*
	Note that the opposite is called empty() and is provided by PHP. 
	However, it doesn't work to be called as a function, since it's
	technically a language construct. It's also impossible to define the
	function called "empty", since it looks wrong to the parser. 
	Therefore we give you "not($thing)" which is nothing other than a
	composable logical not. Though PHP defines "and" and "or" as
	low-precedence logical operators, the only negation is the
	exclamation point, so this does not conflict with the language.
*/
function not($x) {
	return !$x;
}






/**
	
	There are a great many functions we'll define that iterate over
	lists. There are a very few general patterns that describe most of
	that iteration.
	
	Therefore, at this point, I'm going to define some metafunctions. 
	That is, these functions literally concoct new functions based on
	the inputs. To top it all off, there are a few sub-metafunctions.
	
	Most list-building and list-transformation functions use a
	while-list-each form.  You'll notice a lack of reset() calls. This
	is fine because any array passed into a function is implicitly reset
	during the copy.
*/



function _FP_Guard($guards) {
	if (!is_array($guards)) return;
	while (list($cond, $res) = each($guards)) $body .= "if ($cond) return $res;\n";
	return $body;
}


function metameta($name, $args, $result, $guards, $body) {
	$guards = _FP_Guard($guards);
	eval("
		function $name($args) {
			$guards
			$body
			return $result;
		}
	");
}


function metafold($name, $args, $imperative, $init="", $guards="") {
	// The plan here is to build a formulaic function definition from a
	// few basic parts. Then, we eval() the code to bring our creation
	// in to being.
	
	// $in is the input array, and $a represents a running
	// accumulator. The accumulator gets returned at the
	// end of the function.
	
	metameta($name, $args, '$a', $guards, "
		$init;
		while (list(\$k, \$v) = each(\$in)) $imperative;
	");
}

function metamap($name, $args, $imperative, $guards="") {
	// We have a similar plan as above, but with more in the basic
	// pattern. We presume to be converting one list/hash to another,
	// so it makes semantic sense to initialize $out=array();
	// and return that later on.
	
	metameta($name, $args, '$out', $guards, "
		\$out = array();
		while (list(\$k, \$v) = each(\$in)) $imperative;
	");
}

function metascan($name, $args, $imperative, $default) {
	// For things like first, any, and all:
	
	metameta($name, $args, $default, '', "
		while (list(\$k, \$v) = each(\$in)) $imperative;
	");
}



// Typical of FP is applying an operation to a list or hash.

metamap('map', '$func, $in', '$out[$k] = $func($v)', array('!is_array($in)' => 'array()'));

// It happens often that you really want a printf format string instead of a
// function:

metamap('asprintf', '$fmt, $in', '$out[k] = sprintf($fmt, $v)', array('!is_array($in)' => 'array()'));


/**
	Frequently enough I've found the desire to operate on key-value pairs.
	Here's a version of map() tailored to those circumstances. It passes
	$key, $value to your function and the result array has the original keys.
*/

metamap('map_hash', '$func, $in', '$out[$k] = $func($k, $v)', array('!is_array($in)' => 'array()'));




/**
	The next standard operation is filtering. Haskel, perl, and in a way
	shell call this "grep", as do I. Smalltalk seems to call it
	"select", but that's a reserved word in php4. Apparently some
	languages use the word "filter" but I'm a proponent of picking the
	shorter of two equally obvious choices.
*/
metamap('grep', '$func, $in', 'if ($func($v)) $out[$k] = $v');


/**
	Smalltalk also contributes reject(), which is just the opposite: it
	keeps the elements that fail the test.
*/
metamap('reject', '$func, $in', 'if (!$func($v)) $out[$k] = $v');



/**
	The most typical grep is for non-null (logically true) elements.
	Here's my chosen shortcut:
*/
function collect($arr) {
	return grep('id', $arr);
}





/*
	Then there's the topic of list reduction. That's a fancy way to say
	that each of the elements of the list, in turn, go into producing
	some result which is not a list, but rather atomic. Atoms in PHP are
	all either strings or numbers. There are a few PHP primatives such
	as implode() and count() which are reasonable, but there are also a
	few missing ones. We give straightforward efficient implementations
	for the standard academic examples (largely to prevent you from
	reimplementing them), then provide a couple of more generic
	reduction functions.
*/


// "concat()" is really a missing array reduction operation in PHP. It's
// closely related to implode(), so that's a part of my implementation.
//
// The separator idea is for that all-too-common case where you need to add
// "\n" or "<br>\n" or somesuch after everything in your list. It's a fairly
// simple shortcut that's better than building a custom function.

function concat($arr, $sep="") {
	$out = implode($sep, $arr);
	if (count($arr)) $out .= $sep;
	return $out;
}


// The textbooks all have you define sum() recursively. Please don't. If you
// desperately need it, use this instead:

metafold('sum', '$in', '$a += $v', '$a = 0');




/*
	There are a few ways to write the generic reduce() function.  In
	every case, it has to take a function and a list. In keeping with
	the precedent set by other functions around here, we'll take the
	function (by name) first, then the array. An optional third argument
	can be an initial state for the internal accumulator.
	
	It's easiest to see how this works with an example, so here's the
	first form of a reduce() function.
*/

metafold('reduce', '$func, $in, $a=""', '$a = $func($a, $v)');


/*
	In the typical case, the passed function accepts two arguments.  To
	keep things simple, we'll have the first argument be the accumulator
	and the second be the current value to operate on. That way it looks
	similar to a math operation.
	
	It's useful to extend this idea to hashes, which are defined as a
	collection of key/value pairs. In this case, the passed function must
	take three arguments. Since we normally think "Key, Value" that's the
	order of arguments here, subject to the "Accumulator first" rule.
*/

metafold('reduce_hash', '$func, $in, $a=""', '$a = $func($a, $k, $v)');


/*
	Note that there's a subtle difference between starting off the
	iteration with an empty accumulator versus starting the accumulator
	with the first value, which gets consumed.
	
	For example, reduce("min", $list) will tend to return the empty string
	because it's less than any positive number.
	
	We'd rather not place any arbitary limits on where you have to set a
	variable and operate on it. Ideally, you should be able to express
	an entire program in terms of horrendously nested function calls,
	plus lambda functions. Obviously no one should actually attempt that
	outside of an obfuscated code contest, but there are enough list
	reductions that operate on pairs from the same domain that it makes
	sense to provide the capability.
	
	For now, we'll call it freduce. If anyone has a better name, please
	tell me.
*/

function freduce($func, $arr) {
	list(,$accum) = each($arr);
	while (list(,$val) = each($arr)) $accum = $func($accum, $val);
	return $accum;
}




///////////////////////////////////////
//
//  CURRYING OPERATORS
//
///////////////////////////////////////



/*
	Here's the standard typical curry function. You curry a function of
	N arguments down to a "partial" function of N-1 arguments, with one
	of the inputs to the original function held constant at the $clamp
	value.
	
	This is not PRECISELY what some languages mean with currying,
	but this is useful enough in most circumstances.
	
	The curry() function clamps the left-most parameter of a function. 
	Currently it will not work right if you have zero remaining
	arguments. It can clamp with any type of input.
	
*/
function curry($func, $clamp, $remaining=1) {
	$args = _FP_arglist($remaining);
	$clamptext = _FP_argQuote($clamp);
	return lam($args, "$func($clamptext, $args)");
}

/*
	Periodically you want to clamp the last parameter instead of the
	first.
*/
function rcurry($func, $clamp, $remaining=1) {
	$args = _FP_arglist($remaining);
	$clamptext = _FP_argQuote($clamp);
	return lam($args, "$func($args, $clamptext)");
}

// This is just a (potentially useful) shortcut.
function curry2($func, $clamp1, $clamp2, $remaining=1) {
	return curry_array($func, array($clamp1, $clamp2), $remaining);
}


/*
	So lets say you want to "curry" several arguments off a function. 
	You can pass curry_array() an array of the arguments to be clamped.
	The "$remaining" parameter is still the number of arguments to the
	resulting function. You may wonder why not handle this all in one
	call? Well, I'm getting there later on.
*/
function curry_array($func, $clamps, $remaining) {
	$args = _FP_arglist($remaining);
	$clamptext = implode(", ", map("_FP_argQuote", $clamps));
	return lam($args, "$func($clamptext, $args)");
}


function rcurry_array($func, $clamps, $remaining) {
	$args = _FP_arglist($remaining);
	$clamptext = implode(", ", map("_FP_argQuote", $clamps));
	return lam($args, "$func($args, $clamptext)");
}



/*
	There are some operations which php3 just can't do. First among
	those is writing functions with variable numbers of arguments. I
	strongly believe that php4 got it wrong, and should have adopted a
	convention similar to tcl's special "args" formal parameter.
	In fact, the syntax should be (but isn't):
	
	function foo ($bar, $baz, $qux=...) {
		// $qux would be the (possibly empty) array of more arguments.
	}
	
	Since that doesn't work, we have to stick with accepting an optional
	array of extra arguments.
	
	Thus, the interfaces to curry.
*/


// Now then, a few utility functions:
function _FP_arg($id) {
	return '$arg'.$id;
}
function _FP_arglist($count) {
	return implode(", ", map("_FP_arg", range(1, $count)));
}

function _FP_argQuote($arg) {
	if (is_string($arg)) return '"'.addslashes($arg).'"';
	if ($arg == "$arg") return $arg;	// Some kind of number
	
	// In every other case, we can only keep the argument in some
	// special secret place which only the resulting partial function
	// knows about.
	
	static $curry_counter;
	$curry_counter++;
	$GLOBALS["__FPE_CURRIED"][$curry_counter] = $arg;
	return '$GLOBALS["__FPE_CURRIED"]['.$curry_counter.']';
}



////////////////////
//
// General Utility
//
////////////////////

/*
	Unlike most natively functional languages, PHP is not geared toward
	programmatically constructing the list of arguments that get passed
	to a function. However, in many cases that's just the bit of magic
	that gets the job done.
	
	Really recently, php4 provides call_user_func_array($func, $args);
	For the rest of us (and the legibility of our code) we provide here
	the operation which lets us pretend.
	
	Given the name of a function and an array of the intended arguments
	to said function, apply($func, $args) will return the result of the
	function as you probably intended.
*/

if (phpversion() >= 4.05) {
	function apply($func, $args) {
		return call_user_func_array($func, $args);
	}
} else {
	function apply($func, $args) {
		$argstr = "";
		while (list($key, $val) = each($args)) {
			if (!is_int($key)) $key = "'$key'";
			$argstr .= ", \$args[$key]";
		}
		$argstr = substr($argstr, 2);
		eval("\$result = $func($argstr);");
		return $result;
	}
}


// Having said that, it seems that "apply" is one of the most commonly
// curried functions. That means that we can provide a good shortcut which
// doesn't also put the compiler into overdrive, since currying a function
// (or building a lambda of any kind, for that matter) does use up
// resources. The main issue is programmer time, though.

metamap('map_apply', '$func, $in', '$out[$k] = apply($func, $v)');




/**
	This is intended to transpose an arbitrary matrix.  The matrix is
	the arbitrary php array where each value is also a php array.
*/
function transpose($matrix) {
	$out = array();
	while (list($rkey, $row) = each($matrix)) {
		while (list($ckey, $cell) = each($row)) {
			$out[$ckey][$rkey] = $cell;
		}
	}
	return $out;
}



/**
 * This transposes a two-dimensional matrix. Only two dimensional, because
 * otherwise we couldn't use it in weave() and we want to. It dumps the keys
 * because they aren't important.
 */
function _transpose_for_weave($matrix) {
	$j = 0;
	reset($matrix);
	while (list(,$cols) = each($matrix)) {
		$i = 0;
		reset($cols);
		while (list(,$col) = each($cols)) {
			$return[$i][$j] = $col;
			$i++;
		}
		$j++;
	}
	return $return;
}


/*
	There's this thing I call weaving: You apply a function to tuples
	formed from corresponding elements in a group of lists. The results
	get stuffed in a single list, which is returned.
	
	Apparently many languages call this "zip" in the two-list case.
	Of course, that makes me wonder what "unzip" means.
	
	If that's not clear, perhaps this will be:
*/

function weaveN($func, $lists) {
	return map_apply($func, _transpose_for_weave($lists));
}



/*
	The next two functions here are the common special cases. They are
	here in the name of invokation simplicity.
*/

function weave($func, $left, $right) {
	$out = array();
	
	$len = max(count($left), count($right));
	for ($i=0; $i<$len; $i++) {
		list(,$l_atom) = each($left);
		list(,$r_atom) = each($right);
		$out[] = $func($l_atom, $r_atom);
	}
	
	return $out;
}

function zip($func, $left, $right) {
	return weave($func, $left, $right);
}


function weave3($func, $alist, $blist, $clist) {
	$out = array();
	
	$len = max(count($alist), count($blist), count($clist));
	for ($i=0; $i<$len; $i++) {
		list(,$a_atom) = each($a_list);
		list(,$b_atom) = each($b_list);
		list(,$c_atom) = each($c_list);
		$out[] = $func($a_atom, $b_atom, $c_atom);
	}
	
	return $out;
}


///////////////////////////////////

/*
	Some more handy things would be the boolean shortcut reduction
	operators "any()", "all()", and "none()", which incedentally might
	frequently be given the identity function.
	
	and the filtration-related "first()" and "first_key()"
*/

metascan('any', '$func, $in', 'if ($func($v)) return TRUE', 'FALSE');

metascan('all', '$func, $in', 'if (!$func($v)) return FALSE', 'TRUE');

metascan('none', '$func, $in', 'if ($func($v)) return FALSE', 'TRUE');


function first($func, $arr) {
	while (list(,$value) = each($arr)) if ($func($value)) return $value;
}

function first_key($func, $arr) {
	while (list($key, $value) = each($arr)) if ($func($value)) return $key;
}


///////////////////////////////////

/**
	Object Functional Mappings
*/


/**
	The equivalent of map() for method calls.
*/
metamap('inject', '&$object, $method, $in', '$out[$k] = $object->$method($v)');
metamap('inject_hash', '&$object, $method, $in', '$out[$k] = $object->$method($k, $v)');


/**
	Like unrolling a cinnamon roll.
	
	This calls a method repeatedly, collecting return values into a list
	as long as they pass the given test. The default is 'id', which
	means all true values. I suppose a common problem is the string "0",
	and I just might be motivated to do something about if that becomes
	a major issue. On the other hand, strlen does an excelent job
	testing for that case...
*/
function unroll(&$object, $method, $test='id') {
	// Build a list from return values until false
	$out = array();
	while ($test($atom = $object->$method())) $out[] = $atom;
	return $out;
}


//////////////////////////////////

/**
	Here, there are some unsorted functions.
*/

// The canonical "prepend" operation. Surely no faster than linear time,
// whereas a list-oriented language would do this in constant time.
// -- DB
function cons($val, $list) {
	return array_merge(array($val), $list);
}

// Prepends a key/value pair to the head of a hash.
// -- DB
function cons_hash($key, $val, $hash) {
	return array_merge(array($key => $val), $hash);
}


/*
	Apparenly it happens that you want to turn separate lists of keys
	and values into a single hash.
*/
function pair($keys, $values) {
	$out = array();
	while (list(,$k) = each($keys)) {
		list(,$v) = each($values);
		$out[$k] = $v;
	}
	return $out;
}


/**
	90% of lambda functions take one argument:
*/

function fx($body) {
	return lam('$x', $body);
}


// Compute f(g()) where f and g are hashes instead of functions.

metamap('compose', '$f, $in', '$out[$k] = $f[$v]');


// Reverses a list. -- DB
// Note that you lose your keys. -- IK
metamap('reverse', '$in', 'array_unshift($out, $v)');

// Isn't there something in PHP that does this but preserves keys?
// In any case, a special function could be written that did so.
// It would use count() and prev() to step backwards through the input.




// Flattens a list. -- DB
metamap('flatten', '$in', '$out = array_merge($out, $v)');




?>