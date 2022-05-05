<?php

	namespace Traineratwot\cc;


	class Config
	{
		/**
		 * @param $name
		 * @param $value
		 * @param $namespace
		 * @return void
		 */
		static function set($name, $value, $namespace = NULL)
		{
			if (!$namespace and defined('CC_PROJECT_NAME')) {
				$namespace = CC_PROJECT_NAME;
			}
			$const = self::getConstKey($name, $namespace);
			if (!defined($const)) {
				define($const, $value);
				$const2 = self::getConstKey($name);
				if (!defined($const2)) {
					define($const2, $value);
				}
				return true;
			}
			if (function_exists('runkit_constant_redefine')) {
				runkit_constant_redefine($const, $value);
				return true;
			}
			return false;
		}

		/**
		 * @param $name
		 * @param $namespace
		 * @return mixed
		 * @noinspection PhpDocMissingThrowsInspection
		 * @noinspection PhpUnhandledExceptionInspection
		 */
		static function get($name, $namespace = NULL, $default = NULL)
		{
			if (!$namespace and defined('CC_PROJECT_NAME')) {
				$namespace = CC_PROJECT_NAME;
			}
			$const = self::getConstKey($name, $namespace);
			if (defined($const)) {
				return constant($const);
			}
			$const = self::getConstKey($name);
			if (defined($const)) {
				return constant($const);
			}
			return $default;
		}

		static function getConstKey($name, $namespace = NULL)
		{
			$name = strtr("cc_" . $namespace . '_' . $name, [
				'\\' => '_',
				'/'  => '_',
				'-'  => '_',
				' '  => '_',
				'*'  => '_',
				'.'  => '_',
				'+'  => '_',
			]);
			$name = str_replace('__','_',$name);
			return strtoupper($name);
		}
	}