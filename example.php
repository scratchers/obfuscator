<?php
$example = 'some $string';
$test = '$abc 123' . $example . '$hello here I "$am"';

if(isset($_POST['something'])){
  echo $_POST['something'];
}

function exampleFunction($variable2){
  echo $variable2;
}

exampleFunction($example);

$variable3 = array('example','another');

foreach($variable3 as $key => $var3val){
  echo $var3val."somestring";
}

$test = "example";
$$test = 'hello';

exampleFunction($example);
exampleFunction($$test);

function getNewName()
{
    return "test";
}
exampleFunction(${getNewName()});
