<?php

	namespace test;

	use Traineratwot\Cache\Cache;

	require "vendor/autoload.php";

	echo '<pre>';
	var_dump(Cache::getKey('test'));
	die;
