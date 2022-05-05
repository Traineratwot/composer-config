<?php

	namespace Traineratwot\cc;


	use Composer\Autoload\AutoloadGenerator;
	use Composer\Composer;
	use Composer\EventDispatcher\EventDispatcher;
	use Composer\Factory;
	use Composer\IO\IOInterface;
	use Composer\Json\JsonFile;
	use Exception;

	class AutoloadGeneratorWithConfig extends AutoloadGenerator
	{

		private string   $configPath = '';
		private Composer $composer;

		public function __construct(Composer $composer, EventDispatcher $eventDispatcher, IOInterface $io = NULL)
		{
			$this->composer = $composer;
			parent::__construct($eventDispatcher, $io);
		}

		/**
		 * @param string $vendorPathToTargetDirCode
		 * @param string $suffix
		 * @return string
		 * @throws Exception
		 */
		protected function getAutoloadFile(string $vendorPathToTargetDirCode, string $suffix)
		{
			$lastChar = $vendorPathToTargetDirCode[strlen($vendorPathToTargetDirCode) - 1];
			if ("'" === $lastChar || '"' === $lastChar) {
				$vendorPathToTargetDirCode = substr($vendorPathToTargetDirCode, 0, -1) . '/autoload_real.php' . $lastChar;
			} else {
				$vendorPathToTargetDirCode .= " . '/autoload_real.php'";
			}
			$config = '';
			if ($this->configPath) {
				$config = "require_once  __DIR__ .\"/traineratwot/composer-config/src/Config.php\";// include config class \n ";
				$config .= "require_once  __DIR__ .\"/" . $this->getConfigPath() . "\"; // include user config file";
			}
			$projectName = $this->composer->getPackage()->getName();
			return <<<AUTOLOAD
<?php

// autoload.php @generated by Composer

if (PHP_VERSION_ID < 50600) {
    echo 'Composer 2.3.0 dropped support for autoloading on PHP <5.6 and you are running '.PHP_VERSION.', please upgrade PHP or use Composer 2.2 LTS via "composer self-update --2.2". Aborting.'.PHP_EOL;
    exit(1);
}


//start Modyfied by composer-config
if(!defined('CC_PROJECT_NAME')){
	define('CC_PROJECT_NAME', '$projectName'); //set default namespace
}
{$config} 
//end Modyfied by composer-config
require_once $vendorPathToTargetDirCode;

return ComposerAutoloaderInit$suffix::getLoader();

AUTOLOAD;
		}

		/**
		 * @throws Exception
		 */
		public function getConfigPath()
		{
			if ($this->configPath) {
				if (!file_exists($this->configPath)) {
					throw new Exception("File '$config' does not exist");
				}
				if (strpos($this->configPath, '.php') === FALSE) {
					throw new Exception("File '$config' must be a valid PHP file");
				}
			}
			$json = new JsonFile(Factory::getComposerFile());
			$b    = $json->getPath();
			return $this->getRelativePath($b, $this->configPath);
		}

		/**
		 * @throws Exception
		 */
		public function setConfigPath($config)
		{
			$this->configPath = $config;
		}

		function getRelativePath($from, $to)
		{
			// some compatibility fixes for Windows paths
			$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
			$to   = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
			$from = str_replace('\\', '/', $from);
			$to   = str_replace('\\', '/', $to);

			$from    = explode('/', $from);
			$to      = explode('/', $to);
			$relPath = $to;

			foreach ($from as $depth => $dir) {
				// find first non-matching dir
				if ($dir === $to[$depth]) {
					// ignore this directory
					array_shift($relPath);
				} else {
					// get number of remaining dirs to $from
					$remaining = count($from) - $depth;
					if ($remaining > 1) {
						// add traversals up to first matching dir
						$padLength = (count($relPath) + $remaining - 1) * -1;
						$relPath   = array_pad($relPath, $padLength, '..');
						break;
					} else {
						$relPath[0] = './' . $relPath[0];
					}
				}
			}
			return implode('/', $relPath);
		}
	}