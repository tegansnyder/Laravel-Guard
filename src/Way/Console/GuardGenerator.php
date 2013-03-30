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
     * GuardFile
     *
     * @var Way\Console\Guardfile
     */
    protected $guardFile;

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
    public function __construct(Filesystem $file, Config $config, Guardfile $guardFile)
    {
        $this->file = $file;
        $this->config = $config;
        $this->guardFile = $guardFile;

        $this->setPaths();
    }

    /**
     * Set common folder paths
     *
     * @return void
     */
    protected function setPaths()
    {
        $this->jsPath = $this->config->get('guard-laravel::guard.js_path');
        $this->cssPath = $this->config->get('guard-laravel::guard.css_path');
        $this->assetsPath = $this->config->get('guard-laravel::guard.assets_path');
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
        // Display Guardfile plugins alphabetically
        sort($plugins);

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
                $stubs[] = $this->getConcatStub($plugin);
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
     * @param  string $plugin
     * @return string
     */
    protected function getConcatStub($plugin)
    {
        $language = substr($plugin, 7) === 'js' ? 'js' : 'css';

        $files = $this->config->get("guard-laravel::guard.{$language}_concat");
        $files = $this->guardFile->removeFileExtensions($files);

        // Now, we'll grab the concat stub, and replace it with the
        // Ruby-formatted array of JS files.
        return str_replace('{{files}}', implode(' ', $files), $this->getStub("concat-{$language}"));
    }

}