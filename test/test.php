<?php

	namespace test;

	use Traineratwot\cc\Config;

	require "vendor/autoload.php";

	echo '<pre>';
	var_dump(Config::get('test'));
	die;
