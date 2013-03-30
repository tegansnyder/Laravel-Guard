<?php namespace Way\Console;

use Illuminate\Filesystem\Filesystem;

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
	 * Update Guardfile concat plugins with new file list
	 *
	 * @param  string $type
	 * @param  array $fileList
	 * @param string $content
	 * @return string
	 */
	public function updateConcatPlugin($type, array $fileList, $content = null)
	{
		$content = $content ?: $this->getContents();

		$fileList = $this->removeMergedFilesFromList($fileList);

		return preg_replace_callback(
			'/(?<=guard :concat, type: "' . $type . '", files: %w\[).+?(?=\])/i',
			function($matches) use($fileList) {
				// We need Ruby specific array formatting.
				// The concat plugin doesn't want file extensions. :(
				return implode(' ', $this->removeFileExtensions($fileList));
			},
			$content
		);
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
		}, array_diff($fileList, array('scripts.min', 'style.min')));
	}

}