<?php

 $variable_names_before = array();
 $variable_names_after  = array();
 $function_names_before = array();
 $function_names_after  = array();
 $forbidden_variables = array(
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
 $forbidden_functions = array(
     'unlink'
 );

 // read file
 $data = file_get_contents("example.php");

 $lock = false;
 $lock_quote = '';
 for($i = 0; $i < strlen($data); $i++)
 {
     // check if there are quotation marks
     if(($data[$i] == "'" || $data[$i] == '"'))
     {
         // if first quote
         if($lock_quote == '')
         {
             // remember quotation mark
             $lock_quote = $data[$i];
             $lock = true;
         }
         else if($data[$i] == $lock_quote)
         {
             $lock_quote = '';
             $lock = false;
         }
     }

     // detect variables
     if(!$lock && $data[$i] == '$')
     {
         $start = $i;
         // detect variable variable names
         if($data[$i+1] == '$')
         {
             $start++;
             // increment $i to avoid second detection of variable variable as "normal variable"
             $i++;
         }

         $end = 1;
         // find end of variable name
         while(ctype_alpha($data[$start+$end]) || is_numeric($data[$start+$end]) || $data[$start+$end] == "_")
         {
             $end++;
         }
         // extract variable name
         $variable_name = substr($data, $start, $end);
         if($variable_name == '$')
         {
             continue;
         }
         // check if variable name is allowed
         if(in_array($variable_name, $forbidden_variables))
         {
             // forbidden variable deteced, do whatever you want!
         }
         else
         {
             // check if variable name already has been detected
             if(!in_array($variable_name, $variable_names_before))
             {
                 $variable_names_before[] = $variable_name;
                 // generate random name for variable
                 $new_variable_name = "";
                 do
                 {
                     $new_variable_name = random_str(rand(5, 20));
                 }
                 while(in_array($new_variable_name, $variable_names_after));
                 $variable_names_after[] = $new_variable_name;
             }
             //var_dump("variable: " . $variable_name);
         }
     }

     // detect function-definitions
     // the third condition checks if the symbol before 'function' is neither a character nor a number
     if(!$lock && strtolower(substr($data, $i, 8)) == 'function' && (!ctype_alpha($data[$i-1]) && !is_numeric($data[$i-1])))
     {
         // find end of function name
         $end = strpos($data, '(', $i);
         // extract function name and remove possible spaces on the right side
         $function_name = rtrim(substr($data, ($i+9), $end-$i-9));
         // check if function name is allowed
         if(in_array($function_name, $forbidden_functions))
         {
             // forbidden function detected, do whatever you want!
         }
         else
         {
             // check if function name already has been deteced
             if(!in_array($function_name, $function_names_before))
             {
                 $function_names_before[] = $function_name;
                 // generate random name for variable
                 $new_function_name = "";
                 do
                 {
                     $new_function_name = random_str(rand(5, 20));
                 }
                 while(in_array($new_function_name, $function_names_after));
                 $function_names_after[] = $new_function_name;
             }
             //var_dump("function: " . $function_name);
         }
     }
 }


$possible_pre_suffixes = array(
    array(
        "prefix" => "= '",
        "suffix" => "'"
    ),
    array(
        "prefix" => '= "',
        "suffix" => '"'
    ),
    array(
        "prefix" => "='",
        "suffix" => "'"
    ),
    array(
        "prefix" => '="',
        "suffix" => '"'
    ),
    array(
        "prefix" => 'rn "', // return " ";
        "suffix" => '"'
    ),
    array(
        "prefix" => "rn '", // return ' ';
        "suffix" => "'"
    )
);
// replace variable names
for($i = 0; $i < count($variable_names_before); $i++)
{
    $data = str_replace($variable_names_before[$i], '$' . $variable_names_after[$i], $data);

    // try to find strings which equals variable names
    // this is an attempt to handle situations like:
    // $a = "123";
    // $b = "a";    <--
    // $$b = "321"; <--

    // and also
    // function getName() { return "a"; }
    // echo ${getName()};
    $name = substr($variable_names_before[$i], 1);
    for($j = 0; $j < count($possible_pre_suffixes); $j++)
    {
        $data = str_replace($possible_pre_suffixes[$j]["prefix"] . $name . $possible_pre_suffixes[$j]["suffix"],
                            $possible_pre_suffixes[$j]["prefix"] . $variable_names_after[$i] . $possible_pre_suffixes[$j]["suffix"],
                            $data);
    }
}
// replace funciton names
for($i = 0; $i < count($function_names_before); $i++)
{
    $data = str_replace($function_names_before[$i], $function_names_after[$i], $data);
}

echo $data;

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
    for ($i = 0; $i < $length; ++$i)
    {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}
