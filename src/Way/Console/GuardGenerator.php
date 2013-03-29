<?php namespace Way\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as Config;

class GuardGenerator {

	/**
	 * Filesystem
	 * @var Illuminate\Filesystem
	 */
	protected $file;


	/**
	 * Configuration
	 * @var Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Path to user's CSS folder
	 *
	 * @var string
	 */
	protected $cssPath;

	/**
	 * Path to user's JS folder
	 *
	 * @var string
	 */
	protected $jsPath;

	/**
	 * Path to user's assets folder
	 *
	 * @var string
	 */
	protected $assetsPath;

	/**
	 * Create a new controller generator instance.
	 *
	 * @param  Illuminate\Filesystem  $file
	 * @return void
	 */
	public function __construct(Filesystem $file, Config $config)
	{
		$this->file = $file;
		$this->config = $config;

		$this->setPaths();
	}

	/**
	 * Set common folder paths
	 *
	 * @return void
	 */
	protected function setPaths()
	{
		// This is bad and temporary. Fetching a package config file isn't working
		// for me, so I'm specifing the full path. It's my fault. Need to check with Taylor. FIXME.
		$this->jsPath = $this->config->get('packages/way/guard-laravel/guard.js_path', 'public/js');
		$this->cssPath = $this->config->get('packages/way/guard-laravel/guard.css_path', 'public/css');
		$this->assetsPath = $this->config->get('packages/way/guard-laravel/guard.assets_path', 'app/assets');
	}

	/**
	 * Generate an asset folder
	 *
	 * @param  string $dir
	 * @return void
	 */
	public function assetFolder($dir)
	{
		$dir = "{$this->assetsPath}/{$dir}";

		if (! file_exists($dir))
		{
			$this->file->makeDirectory($dir, 0777, true);
		}
	}

	/**
	 * Generate the Guardfile and boilerplate
	 *
	 * @param  array  $plugins List of desired plugins
	 * @param  string $path Directory where file will be created
	 * @return void
	 */
	public function guardFile(array $plugins, $path)
	{
		$stubs = $this->getStubs($plugins);

		return $this->file->put($path.'/Guardfile', $stubs);
	}

	/**
	 * Get stubs for requested plugins
	 *
	 * @param  array  $plugins
	 * @return string
	 */
	protected function getStubs(array $plugins)
	{
		$stubs = array();

		foreach($plugins as $plugin)
		{
			// The concat plugin needs special treatment.
			if (starts_with($plugin, 'concat'))
			{
				$stubs[] = $this->getConcatStub(substr($plugin, 7));
				continue;
			}

			$stubs[] = $this->getStub($plugin);
		}

		// Now, we'll stitch them all together
		return implode("\n\n", $stubs);
	}

	/**
	 * Get a single stub and replace paths
	 *
	 * @param  string $plugin Name of plugin to get stub for
	 * @return string
	 */
	protected function getStub($plugin)
	{
		$stubPath = __DIR__ . "/stubs/guard-{$plugin}-stub.txt";
		if (file_exists($stubPath))
		{
			$stub = $this->file->get($stubPath);
			return $this->applyPathsToStub($stub);
		}
	}

	/**
	 * Replace template tags in stub
	 *
	 * @param  string $stub
	 * @return string
	 */
	protected function applyPathsToStub($stub)
	{
		return preg_replace_callback('/{{([a-z]+Path)}}/i', function($property) {
			return $this->$property[1];
		}, $stub);
	}

	/**
	 * Gets the stub for guard-concat
	 *
	 * @param  string $language [css|js]
	 * @return [type]           [description]
	 */
	protected function getConcatStub($language)
	{
		// We need to know which config option to grab
		$path = $language === 'css' ? $this->cssPath : $this->jsPath;

		// We'll either grab from the guard config file, or all files from the js dir
		$files = $this->config->get("packages/way/guard-laravel/$guard.{$language}_concat", $this->file->files($path));

		// The concat plugin expects file names without extensions.
		// Also, never concatenate the combined file. TODO - shouldn't min names.
		$files = array_map(function($file)
		{
			return pathinfo($file, PATHINFO_FILENAME);
		}, array_diff($files, array('scripts.min', 'style.min')));

		// Now, we'll grab the concat stub, and replace it with the
		// Ruby-formatted array of JS files.
		return str_replace('{{files}}', implode(' ', $files), $this->getStub("concat-{$language}"));
	}

}