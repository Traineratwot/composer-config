<?php

	namespace Traineratwot\cc;


	use Exception;

	class Config
	{
		/**
		 * @param $name
		 * @param $value
		 * @param $namespace
		 * @return void
		 * @noinspection PhpDocMissingThrowsInspection
		 * @noinspection PhpUnhandledExceptionInspection
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
				if (!defined($const)) {
					define($const2, $value);
				}
				return;
			}
			if (function_exists('runkit_constant_redefine')) {
				runkit_constant_redefine($const, $value);
				return;
			}
			throw new Exception('Const  "' . $const . '" already defined');
		}

		/**
		 * @param $name
		 * @param $namespace
		 * @return mixed
		 * @noinspection PhpDocMissingThrowsInspection
		 * @noinspection PhpUnhandledExceptionInspection
		 */
		static function get($name, $namespace = NULL)
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
			return NULL;
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
			return strtoupper($name);
		}
	}