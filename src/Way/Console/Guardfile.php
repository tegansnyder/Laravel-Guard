<?php namespace Way\Console;

use Illuminate\Filesystem\Filesystem;

class FileNotFoundException extends \Exception {}

class Guardfile {

	/**
	 * Filesystem Instance
	 *
	 * @var Illuminate\Filesystem\Filesystem
	 */
	protected $file;

	/**
	 * Base path to where Guardfile is store
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Constructor
	 *
	 * @param  Filesystem $file
	 * @param  string $path
	 * @return void
	 */
	public function __construct(Filesystem $file, $path)
	{
		$this->file = $file;
		$this->path	= $path;
	}

	/**
	 * Get the path to the Guardfile
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path.'/Guardfile';
	}

	/**
	 * Get contents of Guardfile
	 *
	 * @return string
	 */
	public function getContents()
	{
		return $this->file->get($this->getPath());
	}

	/**
	 * Update contents of Guardfile
	 *
	 * @param string $contents
	 * @return void
	 */
	public function put($contents)
	{
		$this->file->put($this->getPath(), $contents);
	}

	/**
	 * Get stubs for requested plugins
	 *
	 * @param  array  $plugins
	 * @return string
	 */
	public function getStubs(array $plugins)
	{
		$stubs = array();

		foreach($plugins as $plugin)
		{
	        // The concat plugin needs special treatment.
			$stub = starts_with($plugin, 'concat')
				? $this->getConcatStub($plugin)
				: $this->getPluginStub($plugin);

			$stubs[] = $this->applyPathsToStub($stub);
		}

	    // Now, we'll stitch them all together
		return implode("\n\n", $stubs);
	}

	/**
	 * Gets the stub for a Guard plugin
	 *
	 * @param  string $plugin
	 * @return string
	 */
	public function getPluginStub($plugin)
	{
		$stubPath = __DIR__ . "/stubs/guard-{$plugin}-stub.txt";

		if (file_exists($stubPath))
		{
			return $this->file->get($stubPath);
		}

		throw new FileNotFoundException;
	}

	/**
	 * Gets a compiled stub for a concat plugin
	 *
	 * @param  string $plugin
	 * @return string
	 */
	protected function getConcatStub($plugin)
	{
		// concat-css, concat-js
		$language = substr($plugin, 7);

		$files = $this->getConcatFiles($language);

		return str_replace('{{files}}', implode(' ', $files), $this->getPluginStub("concat-{$language}"));
	}

	/**
	 * Replace template tags in stub
	 *
	 * @param  string $stub
	 * @return string
	 */
	public function applyPathsToStub($stub)
	{
		return preg_replace_callback('/{{([a-z]+?)Path}}/i', function($matches) {
			$language = $matches[1];

			return \Config::get("guard-laravel::guard.{$language}_path");
		}, $stub);
	}

	/**
	 * Update concat plugin signature and saves file
	 *
	 * @param  string $plugin
	 * @return void
	 */
	public function updateSignature($plugin)
	{
		$language = substr($plugin, 7) === 'js' ? 'js' : 'css';

		$files = $this->getConcatFiles($language);

		$stub = $this->compile($files, $language);

		// Final step is to replace the Guardfile function with the updated one
		$stub = preg_replace('/guard :concat, type: "' . $language . '".+/i', $stub, $this->getContents());
		$this->put($stub);
	}

	/**
	 * Gets the stub for guard-concat
	 *
	 * @param  string $plugin
	 * @return string
	 */
	public function getConcatFiles($language)
	{
		$files = \Config::get("guard-laravel::guard.{$language}_concat");

		return $this->removeFileExtensions($this->removeMergedFilesFromList($files));
	}

	protected function compile($files, $language)
	{
	    // Now, we'll grab the concat stub, and replace it with the
	    // Ruby-formatted array of JS files.
		$stub = str_replace('{{files}}', implode(' ', $files), $this->getPluginStub("concat-{$language}"));

		return $this->applyPathsToStub($stub);
	}

	/**
	 * We don't want merged file to ever be included.
	 *
	 * @param  array  $fileList
	 * @return array
	 */
	protected function removeMergedFilesFromList(array $fileList)
	{
		return array_filter($fileList, function($file)
		{
			return ! preg_match('/\.min\.(js|css)$/i', $file);
		});
	}

	/**
	 * Removes extensions from array of files
	 *
	 * @param  array $fileList
	 * @return string
	 */
	public function removeFileExtensions(array $fileList)
	{
		return array_map(function($file)
		{
			return pathinfo($file, PATHINFO_FILENAME);
		}, $fileList);
	}

}