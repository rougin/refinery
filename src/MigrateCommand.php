<?php namespace Rougin\Refinery;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
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
		$this->setName('migrate')
			->setDescription('Migrate the database')
			->addArgument(
				'version',
				InputArgument::OPTIONAL,
				'Migrate to a specified version of the database'
			)->addOption(
				'revert',
				NULL,
				InputOption::VALUE_OPTIONAL,
				'Number of times to revert from the list of migrations'
			);
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
		$latest = NULL;

		$filenames  = array();
		$timestamps = array();

		/**
		 * Searches a listing of migration files and sorts them after
		 */

		$directory = new \RecursiveDirectoryIterator(APPPATH . 'migrations', \FilesystemIterator::SKIP_DOTS);
		foreach (new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST) as $path) {
			$filenames[]  = str_replace('.php', '', $path->getFilename());
			$timestamp = substr($path->getFilename(), 0, 14);

			if ( ! is_numeric($timestamp)) {
				$timestamp = substr($path->getFilename(), 0, 3);
			}

			$timestamps[] = $timestamp;
		}

		sort($filenames);
		sort($timestamps);

		/**
		 * Might get the latest or the specified version or revert back
		 */

		$revert     = ($input->getOption('revert')) ? $input->getOption('revert') + 1 : 1;
		$end        = count($timestamps) - $revert;

		if ($end < 0) {
			$output->writeln('<error>We can\'t revert to that specified version.</error>');
			return;
		}

		$latest     = $timestamps[$end];
		$latestFile = $filenames[$end];

		if ($input->getArgument('version')) {
			$version = $input->getArgument('version');
		}

		/**
		 * Enable migration and change the current version to a latest one
		 */

		$this->_toggle_migration(TRUE);
		$this->_change_version($current, $latest);

		$this->_ci->load->library('migration');
		$this->_ci->migration->current();

		$this->_toggle_migration();

		/**
		 * Show messages of migrated files
		 */

		if ($current == $latest) {
			$output->writeln('<info>Database is up to date.</info>');
			return;
		}

		for ($counter = 0; $counter < count($timestamps); $counter++) {
			if ($current >= $timestamps[$counter]) {
				continue;
			}

			$filename = $filenames[$counter];
			$output->writeln('<info>"' . $filename . '" has been migrated to the database.</info>');
		}

		if ($input->getArgument('version') || $input->getOption('revert')) {
			$output->writeln('<info>Database is reverted back to version ' . $latest . '. (' . $latestFile . ')</info>');
		}
	}

}