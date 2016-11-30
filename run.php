<?php

$tokens = token_get_all(file_get_contents('example.php'));

$registry = array();

$globals = array(
	'$GLOBALS',
	'$_SERVER',
	'$_GET',
	'$_POST',
	'$_FILES',
	'$_COOKIE',
	'$_SESSION',
	'$_REQUEST',
	'$_ENV',
);

// first pass to change all the variable names and function name declarations
foreach($tokens as $key => $element){
	// make sure it's an interesting token
	if(!is_array($element)){
		continue;
	}
	switch ($element[0]) {
		case T_FUNCTION:
			$prefix = '';
			// this jumps over the whitespace to get the function name
			$index = $key + 2;
			break;

		case T_VARIABLE:
			// ignore the superglobals
			if(in_array($element[1], $globals)){
				continue 2;
			}
			$prefix = '$';
			$index = $key;
			break;

		default:
			continue 2;
	}

	// check to see if we've already registered it
	if(!isset($registry[$tokens[$index][1]])){
		// make sure our random string hasn't already been generated
		do {
			$replacement = $prefix.random_str(16);
		} while(in_array($replacement, $registry));

		// map the original and register the replacement
		$registry[$tokens[$index][1]] = $replacement;
	}

	// rename the variable
	$tokens[$index][1] = $registry[$tokens[$index][1]];
}

// second pass to rename all the function invocations
$tokens = array_map(function($element) use ($registry){
	// check to see if it's a function identifier
	if(is_array($element) && $element[0] === T_STRING){
		// make sure it's one of our registered function names
		if(isset($registry[$element[1]])){
			// rename the variable
			$element[1] = $registry[$element[1]];
		}
	}
	return $element;
},$tokens);

// dump the tokens back out to rebuild the page with obfuscated names
foreach($tokens as $token){
	if(is_array($token)){
		echo $token[1];
	} else {
		echo $token;
	}
}

/**
 * http://stackoverflow.com/a/31107425/4233593
 * Generate a random string, using a cryptographically secure
 * pseudorandom number generator (random_int)
 *
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 *
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */
function random_str($length, $keyspace = 'abcdefghijklmnopqrstuvwxyz')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}
