<?php

	namespace Traineratwot\cc;


	class Config
	{
		/**
		 * @param string $name      Config key
		 * @param mixed  $value     Config value
		 * @param null   $namespace namespace default is project name
		 * @param bool   $strict    disable create key without namespace
		 * @return bool
		 */
		static function set($name, $value, $namespace = NULL, $strict = FALSE, $clone=null)
		{
			if (!$namespace and defined('CC_PROJECT_NAME')) {
				$namespace = CC_PROJECT_NAME;
			}
			$const = self::getConstKey($name, $namespace);
			if (!defined($clone)) {
				define($clone, $value);
			}
			if (!defined($const)) {
				define($const, $value);
				if (!$strict) {
					$const2 = self::getConstKey($name);
					if (!defined($const2)) {
						define($const2, $value);
					}
				}
				return TRUE;
			}
			if (function_exists('runkit_constant_redefine')) {
				runkit_constant_redefine($const, $value);
				return true;
			}
			return false;
		}

		/**
		 * @param string $name      Config key
		 * @param null   $namespace namespace default is project name
		 * @param null   $default   default value if key not found
		 * @param bool   $strict    disable ignore namespace if key in namespace not found
		 * @return mixed
		 */
		static function get($name, $namespace = NULL, $default = NULL, $strict = FALSE)
		{
			if (!$namespace and defined('CC_PROJECT_NAME')) {
				$namespace = CC_PROJECT_NAME;
			}
			$const = self::getConstKey($name, $namespace);
			if (defined($const)) {
				return constant($const);
			}
			if (!$strict) {
				$const = self::getConstKey($name);
				if (defined($const)) {
					return constant($const);
				}
			}
			return $default;
		}

		static function getConstKey($name, $namespace = NULL)
		{
			if ($namespace) {
				$namespace = strtolower($namespace);
			}
			$name = strtr("cc_" . $namespace . '_' . $name, [
				'\\' => '_',
				'/'  => '_',
				'-'  => '_',
				' '  => '_',
				'*'  => '_',
				'.'  => '_',
				'+'  => '_',
			]);
			$name = preg_replace("/_+/", '_', $name);
			return strtoupper($name);
		}

		static function getRequired()
		{
			global $CC_OPTIONS;
			return $CC_OPTIONS['required'];
		}

		static function getAllOptions()
		{
			global $CC_OPTIONS;
			return array_merge($CC_OPTIONS['required'], $CC_OPTIONS['optional']);
		}

		static function getOptional()
		{
			global $CC_OPTIONS;
			return $CC_OPTIONS['optional'];
		}

	}