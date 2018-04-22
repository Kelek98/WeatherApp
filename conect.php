<?php

$host='localhost';
$dbuser='root';
$dbpassword='';
$dbname='weather';


try
{
	$conect= new PDO('mysql:host='.$host.';dbname='.$dbname.'',$dbuser,$dbpassword);
	$conect -> query ('SET NAMES utf8');
	$conect -> query ('SET CHARACTER_SET utf8_unicode_ci');	
}

catch(PDOException $Exception)
{
	$Error="Database error";
}

