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

	protected function setPaths()
	{
		$this->cssPath = $this->config->get('guard.css_path', 'public/css');
		$this->jsPath = $this->config->get('guard.js_path', 'public/js');
		$this->assetsPath = $this->config->get('guard.assets_path', 'app/assets');

	}

	public function assetFolder($dir)
	{
		$dir = "{$this->assetsPath}/{$dir}";

		if (! file_exists($dir))
		{
			$this->file->makeDirectory($dir, 0777, true);
		}
	}

	public function guardFile(array $plugins, $path)
	{
		$stubs = $this->getStubs($plugins);

		return $this->file->put($path.'/Guardfile', $stubs);
	}

	protected function getStubs(array $plugins)
	{
		$stubs = array();

		foreach($plugins as $plugin)
		{
			$stubs[] = $this->getStub($plugin);
		}

		// Now, we'll stitch them all together
		return implode("\n\n", $stubs);
	}

	protected function getStub($plugin)
	{
		// The concat plugin needs special treatment.
		if ($plugin === 'concat-js' or $plugin === 'concat-css')
		{
			return $this->getConcatStub(substr($plugin, 7));
		}

		$stubPath = __DIR__ . "/stubs/guard-{$plugin}-stub.txt";
		if (file_exists($stubPath))
		{
			$stub = $this->file->get($stubPath);
			return $this->applyPathsToStub($stub);
		}
	}

	protected function applyPathsToStub($stub)
	{
		return preg_replace_callback('/{{([a-z]+Path)}}/i', function($property) {
			return $this->$property[1];
		}, $stub);
	}

	protected function getConcatStub($language)
	{
		$path = $language === 'css' ? $this->cssPath : $this->jsPath;

		// We'll either grab from the guard config file, or all files from the js dir
		$files = $this->config->get("guard.{$language}_concat", $this->file->files($path));

		// The concat plugin expects file names without extensions.
		// Also, never concatenate the combined file. TODO - shouldn't hardcode name.
		$files = array_map(function($file)
		{
			return pathinfo($file, PATHINFO_FILENAME);
		}, array_diff($files, array('scripts.min', 'style.min')));

		// Now, we'll grab the concat stub, and replace it with the
		// Ruby-specific array of JS files.
		$stub = $this->file->get(__DIR__ . "/stubs/guard-concat-{$language}-stub.txt");
		$stub = $this->applyPathsToStub($stub);

		return str_replace('{{files}}', implode(' ', $files), $stub);
	}

}