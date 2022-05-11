<?php

	namespace test;

	use Traineratwot\cc\Config;

	require "vendor/autoload.php";
	Config::set('test1', 'value1');
	Config::set('test1', 'value2','lb1');
	Config::set('test2', 'value3');
	Config::set('test3', 'value4','lb1');

	echo Config::get('test1').PHP_EOL;//value1
	echo Config::get('test1','lb1').PHP_EOL;//value2
	echo Config::get('test2').PHP_EOL;//value3
	echo Config::get('test2','lb1').PHP_EOL;//value3
	echo Config::get('test3').PHP_EOL;//value4
	echo Config::get('test3','lb1').PHP_EOL;//value4
