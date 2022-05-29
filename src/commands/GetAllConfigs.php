<?php

	namespace Traineratwot\cc\commands;

	use Composer\Command\BaseCommand;
	use Symfony\Component\Console\Formatter\OutputFormatterStyle;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Traineratwot\config\Config;

	class GetAllConfigs extends BaseCommand
	{
		public function configure()
		: void
		{
			$this->setName('getAllConfigs');
			$this->setHelp('Print all configs');
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 * @return int
		 */
		public function execute(InputInterface $input, OutputInterface $output)
		: int
		{
			global $CC_OPTIONS;
			$rows     = [];
			$ofs      = new OutputFormatterStyle();
			$s        = new SymfonyStyle($input, $output);
			$composer = $this->requireComposer();
			$packType = $composer->getPackage()->getType();
			if (in_array(strtolower($packType), ['project', 'composer-plugin', ''], TRUE)) {
				$extra = $composer->getPackage()->getExtra();
				$dir   = dirname($composer->getConfig()->get('vendor-dir'));
				if (array_key_exists('composer-config', $extra) && array_key_exists('configPath', $extra['composer-config'])) {
					$cfg =  $dir . '/' . $extra['composer-config']['configPath'];
					if(file_exists($cfg)) {
						include_once $cfg;
					}
				}
			}

			foreach ($CC_OPTIONS as $type => $value) {
				foreach ($value as $key => $val) {
					foreach ($val as $namespace => $description) {
						$kn = $key . $namespace;
						if (!isset($rows[$kn])) {
							if (in_array(strtolower($packType), ['project', 'composer-plugin', ''], TRUE)) {
								if ($this->isSet($key, $namespace)) {
									$ofs->setForeground('green');
									$isSet = $ofs->apply('yes');
								} else {
									$ofs->setForeground('red');
									$isSet = $ofs->apply('no');
								}
								$rows[$kn] = [$key, $namespace, $description, $type, $isSet];
							} else {
								$rows[$kn] = [$key, $namespace, $description, $type];
							}
						}
					}
				}
			}
			if (in_array(strtolower((string)$packType), ['project', 'composer-plugin', ''], TRUE)) {
				$s->table(['config key', 'namespace', 'description', 'type', 'is set?'], $rows);
			} else {
				$s->table(['config key', 'namespace', 'description', 'type'], $rows);
			}
			return 0;
		}

		public function isSet(string $name, string $namespace)
		: bool
		{
			return Config::isSet($name, $namespace);
		}
	}