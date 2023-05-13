<?php

	namespace Traineratwot\cc;

	use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
	use Traineratwot\cc\commands\ConfigUpdate;
	use Traineratwot\cc\commands\GetAllConfigs;
	use Traineratwot\cc\commands\InitConfig;

	class Cli implements CommandProviderCapability
	{

		public function getCommands()
		{
			return [new InitConfig(), new GetAllConfigs(),new ConfigUpdate()];
		}
	}
