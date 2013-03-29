<?php namespace Way\Console;


use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

class GuardInitCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'guard:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Make a new Guardfile';

	/**
	 * List of desired Guard plugins,
	 * including default plugins
	 *
	 * @var array
	 */
	protected $plugins = array('concat-js', 'uglify', 'phpunit');

	/**
	 * File generator instance
	 *
	 * @var GuardGenerator
	 */
	protected $generate = array();

	/**
	 * Filesystem instance
	 *
	 * @var Illuminate\Filesystem
	 */
	protected $file;

	/**
	 * Gem builder
	 *
	 * @var Way\Console\Gem
	 */
	protected $gem;

	/**
	 * Path to assets directory
	 *
	 * @var string
	 */
	protected $assetsPath;

	/**
	 * Create a new command instance.
	 *
	 * @param GuardGenerator $generate
	 *
	 * @return void
	 */
	public function __construct(GuardGenerator $generate, Filesystem $file, Gem $gem)
	{
		$this->generate = $generate;
		$this->file = $file;
		$this->gem = $gem;
		$this->assetsPath = Config::get('guard.assets_path');

		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if (! $this->hasRuby())
		{
			$this->error('Please install Ruby and RubyGems.');
			exit();
		}

		$this->getDefaultGems();

		// Do they want preprocessing?
		if ($preprocessor = $this->wantsPreprocessing())
		{
			// We need to keep track of which plugins
			// have been requested.
			$this->plugins[] = $preprocessor;

			$this->getGem("guard-{$preprocessor}");
			$this->generate->assetFolder($preprocessor);

			$this->info("Created {$this->assetsPath}/{$preprocessor}");
		}

		// Otherwise, this is just a vanilla CSS project.
		// Let's at least allow them to concatenate.
		else
		{
			$this->plugins[] = 'concat-css';
		}

		// Do they want CoffeeScript?
		if ($this->wantsCoffee())
		{
			// We need to keep track of which plugins
			// have been requested.
			$this->plugins[] = 'coffeescript';

			$this->getGem("guard-coffeescript");
			$this->generate->assetFolder('coffee');

			$this->info("Created {$this->assetsPath}/coffee");
		}

		$this->generate->guardFile($this->plugins, base_path());
		$this->info('Created Guardfile');

		$this->savePluginListToCache();
	}

	protected function hasRuby()
	{
		$ruby = shell_exec('ruby -v');

		return starts_with($ruby, 'ruby');
	}

	protected function getDefaultGems()
	{
		foreach(array('guard', 'guard-uglify', 'guard-phpunit', 'guard-concat') as $gem)
		{
			$this->getGem($gem);
		}
	}

	protected function getGem($gemName)
	{
		if (! $this->gem->exists($gemName))
		{
			$this->info('Installing ' . ucwords($gemName) . '...');
			$this->gem->install($gemName);
			$this->info(ucwords($gemName) . ' Installed.');
		}
	}

	protected function wantsPreprocessing()
	{
		if ($this->confirm('Do you require CSS preprocessing? [yes|no]', false))
		{
			$preprocessor = strtolower($this->ask('Which CSS preprocessor do you want? [sass|less]'));

			while (! $preprocessor or ! in_array($preprocessor, array('sass', 'less')))
			{
				// ask again
				$preprocessor = $this->ask('I did not recognize that preprocessor. Please try again. [sass|less]');
			}

			return $preprocessor;
		}

		return false;
	}

	protected function wantsCoffee()
	{
		return $this->confirm('What about CoffeeScript support? [yes|no]', false);
	}

	protected function savePluginListToCache()
	{
		$cache = app_path().'/storage/guard';
		if (! $this->file->exists($cache))
		{
			$this->file->makeDirectory($cache);
		}

		// We'll store a space separated list of all requested plugins.
		$this->file->put($cache .'/plugins.txt', implode(' ', $this->plugins));
	}

}