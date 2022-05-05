<?php

	namespace Traineratwot\cc;

	use Composer\Composer;
	use Composer\EventDispatcher\EventSubscriberInterface;
	use Composer\IO\IOInterface;
	use Composer\Plugin\PluginEvents;
	use Composer\Plugin\PluginInterface;

	class Plugin implements PluginInterface, EventSubscriberInterface
	{


		public function activate(Composer $composer, IOInterface $io)
		{
			$autoload = $composer->getPackage()->getAutoload();
			if (!array_key_exists('files', $autoload)) {
				$autoload['files'] = [];
			}
			$initPath = __dir__ . DIRECTORY_SEPARATOR . 'ConfigInit.php';
			if (!array_key_exists($initPath, $autoload['files'])) {
				array_unshift($autoload['files'], $initPath);
			}
			$composer->getPackage()->setAutoload($autoload);
			var_dump(__FUNCTION__);
		}

		public function deactivate(Composer $composer, IOInterface $io)
		{
			var_dump(__FUNCTION__);
			// TODO: Implement deactivate() method.
		}

		public function uninstall(Composer $composer, IOInterface $io)
		{
			var_dump(__FUNCTION__);
			// TODO: Implement uninstall() method.
		}

		public function INIT()
		{
			var_dump(__FUNCTION__);
			// TODO: Implement uninstall() method.
		}

		public static function getSubscribedEvents()
		{
			return [
				PluginEvents::INIT => [
					['INIT', 0],
				],
			];
		}
	}