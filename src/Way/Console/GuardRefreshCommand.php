<?php namespace Way\Console;

use Illuminate\Console\Command;
use Illuminate\Config\Repository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GuardRefreshCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'guard:refresh';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh the Guardfile';

	/**
	 * GuardFile instance
	 *
	 * @var Way\Console\GuardFile
	 */
	protected $guardFile;

	/**
	 * Config
	 *
	 * @var Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Guardfile $guardFile, Repository $config)
	{
		parent::__construct();

		$this->guardFile = $guardFile;
		$this->config = $config;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->updateGuardfile();
	}

	/**
	 * Refresh the Guardfile
	 *
	 * @return void
	 */
	protected function updateGuardfile()
	{
		// First, we'll grab an updated list of the JS and CSS
		// files that should be concatenated.
		$jsList = $this->config->get('guard-laravel::guard.js_concat');
		$cssList = $this->config->get('guard-laravel::guard.css_concat');

		// Next, we'll update both the concat-js and concat-css plugins
		// to reflect the new list of files.
		$contents = $this->guardFile->updateConcatPlugin('js', $jsList);
		$contents = $this->guardFile->updateConcatPlugin('css', $cssList, $contents);

		// Lastly, we replace the old Guardfile
		// with the new one.
		$this->guardFile->put($contents);
	}

}