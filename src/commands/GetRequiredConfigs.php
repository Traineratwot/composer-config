<?php

	namespace Traineratwot\cc\commands;

	use Composer\Command\BaseCommand;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;

	class GetRequiredConfigs extends BaseCommand
	{
		protected function configure()
		: void
		{
			$this->setName('getRequiredConfigs');
			$this->addArgument('namespace');
		}

		protected function execute(InputInterface $input, OutputInterface $output)
		: int
		{
			$namespace = $input->getArgument('namespace');
			
			$output->writeln('Executing');
			return 0;
		}
	}