<?php

$example = 'some $string';

function exampleFunction($variable2){
  echo $variable2;
}

$variable3 = array('example','another');

foreach($variable3 as $key => $var3val){
  echo $var3val."somestring";
}
