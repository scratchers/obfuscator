<?php

$example = 'some $string';

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
