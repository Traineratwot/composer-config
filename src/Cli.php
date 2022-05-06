<?php

	namespace Traineratwot\cc;

	use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
	use Traineratwot\cc\commands\GetAllConfigs;
	use Traineratwot\cc\commands\GetRequiredConfigs;

	class Cli implements CommandProviderCapability
	{

		public function getCommands()
		{
			return [new GetAllConfigs(), new GetRequiredConfigs()];
		}
	}