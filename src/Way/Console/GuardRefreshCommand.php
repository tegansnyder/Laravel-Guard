<?php namespace Way\Console;

use Illuminate\Console\Command;
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
	 * Guard Generator instance
	 *
	 * @var GuardGeneartor
	 */
	protected $generate;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(GuardGenerator $generate)
	{
		$this->generate = $generate;

		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->generate->guardFile(explode(' ', $this->getPluginListFromStorage()));
	}

	protected function getPluginListFromStorage()
	{
		return \File::get(app_path() .'/storage/guard/plugins.txt');
	}

}