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
	 * Guard
	 *
	 * @var Way\Console\Guardfile
	 */
	protected $guardFile;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(GuardFile $guardFile)
	{
		parent::__construct();

		$this->guardFile = $guardFile;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// todo can't hardcode these.
		// what if using Less? or none?
		$plugins = array(
			'concat-css',
			'concat-js',
			'coffeescript',
			'sass',
			'less',
			'uglify',
			'refresher'
		);

		foreach($plugins as $plugin)
		{
			$this->guardFile->updateSignature($plugin);
		}
	}

}