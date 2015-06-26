<?php namespace Rougin\Refinery;

use Rougin\Describe\Describe;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMigrationCommand extends Command
{

	private $_describe = NULL;

	public function __construct()
	{
		parent::__construct();

		require APPPATH . 'config/database.php';

		$db['default']['driver'] = $db['default']['dbdriver'];
		unset($db['default']['dbdriver']);

		$this->_describe        = new Describe($db['default']);
		$this->_migrationConfig = APPPATH . '/config/migration.php';
		$this->_migrationFile   = file_get_contents($this->_migrationConfig);
	}

	/**
	 * Add or modify an existing column to a table
	 * 
	 * @param  string $type
	 * @param  array  $parameters
	 * @return string
	 */
	protected function _column($type, $parameters)
	{
		if ($parameters['column'] == 'id') {
			$parameters['type'] = 'INT';
			$parameters['length'] = 10;
			$parameters['auto_increment'] = TRUE;
			$parameters['primary'] = TRUE;
		}

		$column = '$this->dbforge->' . $type . '_column(\'' . $parameters['table'] . '\', array(' . "\n";
		$column .= "\t\t\t" . '\'' . $parameters['column'] . '\' => array(' . "\n";
		$column .= "\t\t\t\t" . '\'type\' => \'' . $parameters['type'] . '\',' . "\n";
		$column .= "\t\t\t\t" . '\'constraint\' => \'' . $parameters['length'] . '\',' . "\n";
		$column .= "\t\t\t\t" . '\'auto_increment\' => ' . $parameters['auto_increment'] . ',' . "\n";

		if (isset($parameters['default']) && $parameters['default'] != '') {
			$default = $parameters['default'];
			$column .= "\t\t\t\t" . '\'default\' => \'' . $default . '\',' . "\n";
		}

		$column .= "\t\t\t\t" . '\'null\' => ' . $parameters['null'] . ',' . "\n";
		$column .= "\t\t\t\t" . '\'unsigned\' => ' . $parameters['unsigned'] . ',' . "\n";
		$column .= "\t\t\t" . ')' . "\n";
		$column .= "\t\t" .  '));';

		if ($parameters['primary']) {
			$column .= "\n\n\t\t" . '$this->dbforge->add_key(\'' . $parameters['column'] . '\', TRUE);';
		}

		return str_replace(",\n\t\t\t" . ')', "\n\t\t\t" . ')', $column);
	}

	/**
	 * Converts camelCase into snake_case
	 * 
	 * @param  string $string
	 * @return string
	 */
	protected function _underscore($string)
	{
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
		$result = $matches[0];

		foreach ($result as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}

		return implode('_', $result);
	}

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('create:migration')
			->setDescription('Create a new migration file')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'Name of the migration file'
			)->addOption(
				'sequential',
				NULL,
				InputOption::VALUE_NONE,
				'Generates a migration file with a sequential identifier'
			)->addOption(
				'type',
				NULL,
				InputOption::VALUE_OPTIONAL,
				'Data type of the column'
			)->addOption(
				'length',
				NULL,
				InputOption::VALUE_OPTIONAL,
				'Length of the column'
			)->addOption(
				'auto_increment',
				NULL,
				InputOption::VALUE_NONE,
				'Generates an "AUTO_INCREMENT" flag on the column'
			)->addOption(
				'default',
				NULL,
				InputOption::VALUE_OPTIONAL,
				'Generates a default value in the column definition'
			)->addOption(
				'null',
				NULL,
				InputOption::VALUE_NONE,
				'Generates a "NULL" value in the column definition'
			)->addOption(
				'primary',
				NULL,
				InputOption::VALUE_NONE,
				'Generates a "PRIMARY" value in the column definition'
			)->addOption(
				'unsigned',
				NULL,
				InputOption::VALUE_NONE,
				'Generates an "UNSIGNED" value in the column definition'
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
		if ( ! file_exists(APPPATH . 'migrations/')) {
			mkdir(APPPATH . 'migrations');
		}

		$name         = $this->_underscore($input->getArgument('name'));
		$filename     = APPPATH . 'migrations/' . date('YmdHis') . '_' . $name . '.php';
		$isSequential = strpos($this->_migrationFile, '$config[\'migration_type\'] = \'timestamp\'');
		$sequence     = '';

		if ($input->getOption('sequential') || $isSequential === FALSE) {
			$number = 1;
			$files = new \FilesystemIterator(APPPATH . 'migrations/', \FilesystemIterator::SKIP_DOTS);

			if ($files != '') {
				$number = iterator_count($files) + 1;
			}

			$sequence = sprintf('%03d', $number);

			$filename = APPPATH . 'migrations/' . $sequence . '_' . $name . '.php';
		}

		$keywords = explode('_', $name);
		$template = file_get_contents(__DIR__ . '/Templates/Migration.txt');

		$search   = array('[name]', '[up]', '[down]');
		$replace  = array($name);

		if ($keywords[0] != 'create') {
			$parameters['column']         = $keywords[1];
			$parameters['table']          = $keywords[3];
			$parameters['type']           = ($input->getOption('type')) ? $input->getOption('type') : 'VARCHAR';
			$parameters['length']         = ($input->getOption('length')) ? $input->getOption('length') : 50;
			$parameters['auto_increment'] = ($input->getOption('auto_increment')) ? 'TRUE' : 'FALSE';
			$parameters['default']        = ($input->getOption('default')) ? $input->getOption('default') : '';
			$parameters['null']           = ($input->getOption('null')) ? 'TRUE' : 'FALSE';
			$parameters['primary']        = ($input->getOption('primary')) ? TRUE : FALSE;
			$parameters['unsigned']       = ($input->getOption('unsigned')) ? 'TRUE' : 'FALSE';
		}

		switch ($keywords[0]) {
			case 'create':
				$table = $keywords[1];

				$replace[] = '$this->dbforge->add_field(\'id\');' . "\n\t\t" .
					'$this->dbforge->create_table(\'' . $table . '\');';
				$replace[] = '$this->dbforge->drop_table(\'' . $table . '\');';

				break;
			case 'add':
				$replace[] = $this->_column('add', $parameters);
				$replace[] = '$this->dbforge->drop_column(\'' . $parameters['table'] . '\', \'' . $parameters['column'] . '\');';

				break;
			case 'delete':
				$replace[] = '$this->dbforge->drop_column(\'' . $parameters['table'] . '\', \'' . $parameters['column'] . '\');';
				$replace[] = $this->_column('add', $parameters);

				break;
			case 'modify':
				$replace[] = $this->_column('modify', $parameters);
				$tableInformation = $this->_describe->getInformationFromTable($parameters['table']);

				foreach ($tableInformation as $row) {
					if ($row->getField() == $parameters['column']) {
						$parameters['type']           = $row->getDataType();
						$parameters['length']         = $row->getLength();
						$parameters['auto_increment'] = ($row->isAutoIncrement()) ? 'TRUE' : 'FALSE';
						$parameters['null']           = ($row->isNull()) ? TRUE : FALSE;
						$parameters['default']        = ($row->getDefaultValue()) ? $row->getDefaultValue() : '';
						$parameters['primary']        = ($row->isPrimaryKey()) ? TRUE : FALSE;
					}
				}

				$replace[] = $this->_column('modify', $parameters);

				break;
		}

		$content = str_replace($search, $replace, $template);
		$file = fopen($filename, 'wb');
		file_put_contents($filename, $content);

		$filename = str_replace(APPPATH . 'migrations/', '', $filename);
		$output->writeln('<info>"' . $filename . '" has been created.</info>');
	}

}