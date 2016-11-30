<?php

// comment here

$example = 'some $string';

if(isset($_POST['something'])){
	echo /* another comment */ $_POST['something'];
}

function exampleFunction($variable2){
  echo $variable2;
}

/*
  $multi-line omment
*/

exampleFunction($example);

$variable3 = array('example','another');

foreach($variable3 as $key => $var3val){
  echo $var3val."somestring";
}
