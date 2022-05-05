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

	class Plugin implements PluginInterface, EventSubscriberInterface, Capable
	{


		private AutoloadGeneratorWithConfig $autoloadGeneratorWithConfig;
		private Composer                    $composer;
		private IOInterface                 $io;

		/**
		 * @throws Exception
		 */
		public function activate(Composer $composer, IOInterface $io)
		{
			$this->composer                    = $composer;
			$this->io                          = $io;
			$package                           = $composer->getPackage();
			$extra                             = $package->getExtra();
			$process                           = new ProcessExecutor($io);
			$dispatcher                        = new EventDispatcher($composer, $io, $process);
			$this->autoloadGeneratorWithConfig = new AutoloadGeneratorWithConfig($composer, $dispatcher, $this->io);


			if (!array_key_exists('cc', $extra)) {
				$extra['cc'] = [];
			}
			if (array_key_exists('configPath', $extra['cc'])) {
				$this->autoloadGeneratorWithConfig->setConfigPath($extra['cc']['configPath']);
			} else {
				$extra['cc']['configPath'] = $io->ask("Set config path? [enter to skip]");
			}

			$this->composer->setAutoloadGenerator($this->autoloadGeneratorWithConfig);
			$this->setExtra(array_merge($extra['cc'], ["test" => 11]));
		}

		public
		function setExtra($value)
		{
			$json        = new JsonFile(Factory::getComposerFile());
			$manipulator = new JsonManipulator(file_get_contents($json->getPath()));
			$manipulator->addSubNode('extra', 'cc', $value);
			file_put_contents($json->getPath(), $manipulator->getContents());
		}

		public
		function deactivate(Composer $composer, IOInterface $io)
		{
			// TODO: Implement deactivate() method.
		}

		public
		function uninstall(Composer $composer, IOInterface $io)
		{
			$process           = new ProcessExecutor($io);
			$dispatcher        = new EventDispatcher($composer, $io, $process);
			$autoloadGenerator = new AutoloadGeneratorWithConfig($dispatcher, $this->io);
			$this->composer->setAutoloadGenerator($autoloadGenerator);
			$this->setExtra([]);
		}

		public
		function INIT(Event $event)
		{

		}

		public
		static function getSubscribedEvents()
		{
			return [
				PluginEvents::INIT => [
					['INIT', 0],
				],
			];
		}

		public function getCapabilities()
		{
			return array(
				'Composer\Plugin\Capability\CommandProvider' => 'Traineratwot\cc\Cli',
			);
		}
	}