<?php

	namespace Traineratwot\cc\commands;

	use Composer\Command\BaseCommand;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class GetAllConfigs extends BaseCommand
	{
		protected function configure()
		: void
		{
			$this->setName('getAllConfigs');
			$this->setHelp('Print all configs');
		}

		protected function execute(InputInterface $input, OutputInterface $output)
		: int
		{
			global $CC_OPTIONS;
			$s         = new SymfonyStyle($input, $output);
			$rows      = [];
			foreach ($CC_OPTIONS as $type => $value) {
				foreach ($value as $key => $val) {
					foreach ($val as $namespace => $description) {
						$kn = $key . $namespace;
						if (!isset($rows[$kn])) {
							$rows[$kn] = [$key, $namespace, $description, $type];
						}
					}
				}
			}
			$s->table(['config key', 'namespace', 'description', 'type'], $rows);
			return 0;
		}
	}