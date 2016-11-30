<?php

$tokens = token_get_all(file_get_contents('example.php'));

$registry = array(
	'original' => array(),
	'replaced' => array(),
);

$tokens = array_map(function($element) use (&$registry){
	// check to see if it's a variable
	if(is_array($element) && $element[0] === T_VARIABLE){
		// check to see if we've already registered it
		if(!isset($registry['original'][$element[1]])){
			// make sure our random string hasn't already been generated
			do {
				$replacement = '$'.random_str(6);
			} while(in_array($replacement, $registry['replaced']));

			// map the original and register the replacement
			$registry['original'][$element[1]] = $registry['replaced'][] = $replacement;
		}

		// rename the variable
		$element[1] = $registry['original'][$element[1]];
	}

	return $element;
},$tokens);

// dump the tokens back out to rebuild the page with obfuscated variables
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
function random_str($length, $keyspace = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}
