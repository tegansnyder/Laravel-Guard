<?php namespace Way\Console;

use Illuminate\Support\ServiceProvider;

class GuardLaravelServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerMake();
		$this->registerWatch();
		$this->registerRefresh();

		$this->registerCommands();
	}

	/**
	 * Register guard.make
	 *
	 * @return Way\Console\GuardInitCommand
	 */
	protected function registerMake()
	{
		$this->app['guard.make'] = $this->app->share(function($app)
		{
			$guardFile = new Guardfile($app['files'], base_path());
			$generator = new GuardGenerator($app['files'], \Config::getFacadeRoot(), $guardFile);
			$gem = new Gem;

			return new GuardInitCommand($generator, $app['files'], $gem);
		});
	}

	/**
	 * Register guard.watch
	 *
	 * @return Way\Console\GuardWatchCommand
	 */
	protected function registerWatch()
	{
		$this->app['guard.watch'] = $this->app->share(function($app)
		{
			return new GuardWatchCommand;
		});
	}

	/**
	 * Register guard.refresh
	 *
	 * @return Way\Console\GuardRefreshCommand
	 */
	protected function registerRefresh()
	{
		$this->app['guard.refresh'] = $this->app->share(function($app)
		{
			$guardFile = new Guardfile($app['files'], base_path());

			return new GuardRefreshCommand($guardFile, $app['config']);
		});
	}

	/**
	 * Make commands visible to Artisan
	 *
	 * @return void
	 */
	protected function registerCommands()
	{
		$this->commands(
			'guard.make',
			'guard.watch',
			'guard.refresh'
		);
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('way/guard-laravel');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}