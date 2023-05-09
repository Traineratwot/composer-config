<?php

    namespace Traineratwot\cc\commands;

    use Composer\Command\BaseCommand;
    use Symfony\Component\Console\Formatter\OutputFormatterStyle;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Component\Console\Style\SymfonyStyle;
    use Traineratwot\config\Config;

    class ConfigUpdate extends BaseCommand
    {
        public function configure()
        : void
        {
            $this->setName('configUpdate');
            $this->setHelp('Update php.inc, for help your IDE detect constants');
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
                $dir   = dirname($composer->getConfig()->get('vendor-dir'));
                $extra = $composer->getPackage()->getExtra();
                if (array_key_exists('composer-config', $extra) && array_key_exists('configPath', $extra['composer-config'])) {
                    $cfg = $dir . '/' . $extra['composer-config']['configPath'];
                    if (file_exists($cfg)) {
                        include_once $cfg;
                    }else if (file_exists(realpath($extra['composer-config']['configPath']))) {
                        include_once realpath($extra['composer-config']['configPath']);
                    }else{
                        $output->writeln('Error:loading configuration');
                    }
                }
                $aliases = Config::$aliases;
                if (!empty($aliases)) {
                    $inc = "<?php\n";
                    foreach ($aliases as $name => $val) {
                        $inc .= <<<INC
	const $name = <<<'TXT'
$val
TXT;

INC;
                    }
                    file_put_contents($dir . '/vendor/cc.config.php', $inc);
                    $output->writeln('ok');
                } else {
                    $output->writeln('Empty config');
                }
            }
            return 0;
        }

        public function isSet(string $name, string $namespace)
        : bool
        {
            return Config::isSet($name, $namespace);
        }
    }
