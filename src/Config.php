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
		static function set($name, $value, $namespace = CC_PROJECT_NAME)
		{
			$const = self::getConstKey($name, $namespace);
			if (!defined($const)) {
				define($const, $value);
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
		static function get($name, $namespace = CC_PROJECT_NAME)
		{
			$const = self::getConstKey($name, $namespace);
			if (defined($const)) {
				return constant($const);
			}
			throw new Exception('Const  "' . $const . '" is not defined');
		}

		static function getConstKey($name, $namespace = CC_PROJECT_NAME)
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