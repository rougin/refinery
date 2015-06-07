<?php namespace Rougin\Refinery;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateResetCommand extends Command
{

	private $_ci              = NULL;
	private $_migrationConfig = NULL;
	private $_migrationFile   = NULL;

	/**
	 * Get the CodeIgniter instance and the migration file
	 */
	public function __construct($codeigniter)
	{
		parent::__construct();

		$this->_ci              = $codeigniter;
		$this->_migrationConfig = APPPATH . '/config/migration.php';
		$this->_migrationFile   = file_get_contents($this->_migrationConfig);
	}

	/**
	 * Change the migration version
	 * 
	 * @param  int $current
	 * @param  int $timestamp
	 */
	protected function _change_version($current, $timestamp)
	{
		$migrationFile = file_get_contents($this->_migrationConfig);

		$currentVersion = '$config[\'migration_version\'] = ' . $current . ';';
		$newVersion = '$config[\'migration_version\'] = ' . $timestamp . ';';

		$migrationFile = str_replace($currentVersion, $newVersion, $migrationFile);

		$file = fopen($this->_migrationConfig, 'wb');
		file_put_contents($this->_migrationConfig, $migrationFile);
		fclose($file);
	}

	/**
	 * Enable/disable the Migration Class
	 * 
	 * @param  boolean $enabled
	 */
	protected function _toggle_migration($enabled = FALSE)
	{
		$migrationFile = file_get_contents($this->_migrationConfig);
		$search        = '$config[\'migration_enabled\'] = TRUE;';
		$replace       = '$config[\'migration_enabled\'] = FALSE;';

		if ($enabled) {
			$search  = '$config[\'migration_enabled\'] = FALSE;';
			$replace = '$config[\'migration_enabled\'] = TRUE;';
		}

		$migrationFile = str_replace($search, $replace, $migrationFile);

		$file = fopen($this->_migrationConfig, 'wb');
		file_put_contents($this->_migrationConfig, $migrationFile);
		fclose($file);
	}

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('migrate:reset')
			->setDescription('Rollback all migrations');
	}

	/**
	 * Execute the command
	 * 
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/**
		 * Search the current migration version
		 */

		preg_match_all('/\$config\[\'migration_version\'\] = (\d+);/', $this->_migrationFile, $match);
		$current = $match[1][0];

		/**
		 * Enable migration and change the current version to 0
		 */

		$this->_toggle_migration(TRUE);
		$this->_change_version($current, 0);

		$this->_ci->load->library('migration');
		$this->_ci->migration->current();

		$this->_toggle_migration();
		$output->writeln('<info>Database has been resetted.</info>');
	}

}