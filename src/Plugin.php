<?php

	namespace Traineratwot\cc;

	use Composer\Composer;
	use Composer\EventDispatcher\Event;
	use Composer\EventDispatcher\EventDispatcher;
	use Composer\EventDispatcher\EventSubscriberInterface;
	use Composer\Factory;
	use Composer\IO\IOInterface;
	use Composer\Json\JsonFile;
	use Composer\Json\JsonManipulator;
	use Composer\Plugin\Capable;
	use Composer\Plugin\PluginEvents;
	use Composer\Plugin\PluginInterface;
	use Composer\Util\ProcessExecutor;
	use Exception;
	use Traineratwot\cc\Cli;

	class Plugin implements PluginInterface, EventSubscriberInterface, Capable
	{


		/**
		 * @var Composer
		 */
		public $composer;
		/**
		 * @var array[]
		 */
		public $options;

		public static function getSubscribedEvents()
		{
			return [
				PluginEvents::INIT => [
					['INIT', 0],
				],
			];
		}

		/**
		 * @throws Exception
		 */
		public function activate(Composer $composer, IOInterface $io)
		{
			$this->composer              = $composer;
			$this->options               = [
				'required' => [],
				'optional' => [],
			];
			$package                     = $composer->getPackage();
			$extra                       = $package->getExtra();
			$process                     = new ProcessExecutor($io);
			$dispatcher                  = new EventDispatcher($composer, $io, $process);
			$autoloadGeneratorWithConfig = new AutoloadGeneratorWithConfig($this, $dispatcher, $io);

			$this->getAllConfigs();
			if (!array_key_exists('composer-config', $extra)) {
				$extra['composer-config'] = [];
			}
			if (in_array(strtolower($package->getType()), ['project', 'composer-plugin', ''], TRUE)) {
				if (!array_key_exists('configPath', $extra['composer-config'])) {
					$extra['composer-config']['configPath'] = $io->ask("Set config path? [enter to skip]");
				}
				$autoloadGeneratorWithConfig->setConfigPath($extra['composer-config']['configPath']);
			}
			$this->composer->setAutoloadGenerator($autoloadGeneratorWithConfig);
		}

		public function getAllConfigs()
		{
			global $CC_OPTIONS;
			$v            = $this->composer->getConfig()->get('vendor-dir');
			$v            = self::pathNormalize($v);
			$components   = glob($v . '*/*/composer.json');
			$components[] = self::pathNormalize(Factory::getComposerFile());
			foreach ($components as $path) {
				try {
					$path = self::pathNormalize($path);
					if ($path) {
						$json = new JsonFile($path);
						$pack = $json->read();
						if (array_key_exists('extra', $pack) and array_key_exists('composer-config', $pack['extra'])) {
							$namespace = $pack['extra']['composer-config']['namespace'] ?? $pack['name'];
							if (array_key_exists('required', $pack['extra']['composer-config'])) {
								foreach ($pack['extra']['composer-config']['required'] as $key => $value) {
									$this->options['required'][$key][$namespace] = $value;
								}
							}
							if (array_key_exists('optional', $pack['extra']['composer-config'])) {
								foreach ($pack['extra']['composer-config']['optional'] as $key => $value) {
									$this->options['optional'][$key][$namespace] = $value;
								}
							}
						}
					}
				} catch (Exception $e) {

				}
			}
			$CC_OPTIONS = $this->options;
		}

		public static function pathNormalize($path, $DIRECTORY_SEPARATOR = "/")
		{
			$path = preg_replace('/(\/+|\\\\+)/m', $DIRECTORY_SEPARATOR, $path);
			if (file_exists($path)) {
				if (is_dir($path)) {
					if (self::getSystem() === 'nix') {
						$path = "/" . trim($path, $DIRECTORY_SEPARATOR) . $DIRECTORY_SEPARATOR;
					} else {
						$path = trim($path, $DIRECTORY_SEPARATOR) . $DIRECTORY_SEPARATOR;
					}
				} elseif (self::getSystem() === 'nix') {
					$path = $DIRECTORY_SEPARATOR . trim($path, $DIRECTORY_SEPARATOR);
				} else {
					$path = trim($path, $DIRECTORY_SEPARATOR);
				}
				return $path;
			}
			return $path;
		}

		public static function getSystem()
		{
			$sys = strtolower(php_uname('s'));
			if (strpos($sys, 'windows') !== FALSE) {
				return 'win';
			}
			return 'nix';
		}

		public function deactivate(Composer $composer, IOInterface $io)
		{
			// TODO: Implement deactivate() method.
		}

		public function uninstall(Composer $composer, IOInterface $io)
		{
			$process           = new ProcessExecutor($io);
			$dispatcher        = new EventDispatcher($composer, $io, $process);
			$autoloadGenerator = new AutoloadGeneratorWithConfig($dispatcher, $io);
			$this->composer->setAutoloadGenerator($autoloadGenerator);
		}

		public function INIT(Event $event)
		{

		}

		public function getCapabilities()
		{
			return [
				'Composer\Plugin\Capability\CommandProvider' => Cli::class,
			];
		}
	}
