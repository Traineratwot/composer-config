<?php

	namespace Traineratwot\cc;

	class Config
	{
		public static array $aliases = [];

		/**
		 * @param string $name      Config key
		 * @param mixed  $value     Config value
		 * @param null   $namespace namespace default is project name
		 * @param bool   $strict    disable create key without namespace
		 * @param null   $clone		User constant name
		 * @return bool
		 */
		public static function set(string $name, $value, $namespace = NULL, bool $strict = FALSE, $clone = NULL)
		: bool
		{
			if (!$namespace && defined('CC_PROJECT_NAME')) {
				$namespace = CC_PROJECT_NAME;
			}
			if ($clone && !defined($clone)) {
				self::$aliases[$clone] = $value;
				define($clone, $value);
			}
			$const = self::getConstKey($name, $namespace);
			if ($const && !defined($const)) {
				self::$aliases[$const] = $value;
				define($const, $value);
				if (!$strict) {
					$const2 = self::getConstKey($name);
					if ($const2 && !defined($const2)) {
						self::$aliases[$const2] = $value;
						define($const2, $value);
					}
				}
				return TRUE;
			}
			if (function_exists('runkit_constant_redefine')) {
				runkit_constant_redefine($const, $value);
				return TRUE;
			}
			return FALSE;
		}

		public static function getConstKey($name, $namespace = NULL)
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

		/**
		 * @param string $name      Config key
		 * @param null   $namespace namespace default is project name
		 * @param null   $default   default value if key not found
		 * @param bool   $strict    disable ignore namespace if key in namespace not found
		 * @return string
		 */
		public static function get(string $name, $namespace = NULL, $default = NULL, $strict = FALSE)
		{
			if (!$namespace && defined('CC_PROJECT_NAME')) {
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

		public static function getRequired()
		{
			global $CC_OPTIONS;
			return $CC_OPTIONS['required'];
		}

		public static function getAllOptions()
		{
			global $CC_OPTIONS;
			return array_merge($CC_OPTIONS['required'], $CC_OPTIONS['optional']);
		}

		public static function getOptional()
		{
			global $CC_OPTIONS;
			return $CC_OPTIONS['optional'];
		}

		public static function isSet($name, $namespace = NULL)
		: bool
		{
			if (!$namespace && defined('CC_PROJECT_NAME')) {
				$namespace = CC_PROJECT_NAME;
			}
			$const = self::getConstKey($name, $namespace);
			if (defined($const)) {
				return TRUE;
			}
			$const = self::getConstKey($name);
			if (defined($const)) {
				return TRUE;
			}
			return FALSE;
		}
	}