<?php

	namespace Traineratwot\cc\commands;

	use Composer\Command\BaseCommand;
	use Composer\Factory;
	use Composer\Json\JsonFile;
	use Composer\Json\JsonManipulator;
	use Exception;
	use Symfony\Component\Console\Formatter\OutputFormatterStyle;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Traineratwot\config\Config;

	class InitConfig extends BaseCommand
	{
		public function configure()
		: void
		{
			$this->setName('InitConfig');
			$this->setHelp('Modify composer.json ');
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 * @return int
		 */
		public function execute(InputInterface $input, OutputInterface $output)
		: int
		{
			$config = null;
			if($input->hasParameterOption('--config')){
				$config = $input->getOption('--config');
			}
			$this->setExtra($config);
			return 0;
		}

		public function setExtra($value = NULL)
		{
			try {
				$json        = new JsonFile(Factory::getComposerFile());
				$manipulator = new JsonManipulator(file_get_contents($json->getPath()));
				$manipulator->addMainKey('$schema', 'https://raw.githubusercontent.com/Traineratwot/composer-config/master/composer-config-schema.json');
				if($value) {
					$manipulator->addSubNode('extra', 'composer-config', $value);
				}
				$manipulator->addSubNode('scripts', 'composer-config-print', 'composer getAllConfigs');
				$manipulator->addSubNode('scripts', 'composer-config-update', 'composer configUpdate');
				file_put_contents($json->getPath(), $manipulator->getContents());
			} catch (Exception $e) {

			}
		}
	}
