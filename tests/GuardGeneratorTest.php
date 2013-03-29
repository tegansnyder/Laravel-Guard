<?php

use Way\Console\GuardGenerator;
use Mockery as m;
use Illuminate\Config\Repository as Config;

class GuardGeneratorTest extends PHPUnit_Framework_TestCase {
	public function tearDown()
	{
		Mockery::close();
	}

	public function testGuardFileCanBeCreated()
	{
		// Mock Config
		$configMock = m::mock('Illuminate\Config\Repository[get]');
		$configMock->shouldReceive('get')->andReturn('path');

		// We don't want to REALLY create files. :)
		$files =  m::mock('Illuminate\Filesystem\Filesystem[put]');

		// We want the call to be made, though, to create the Guardfile with the contents of the stub.
		$files->shouldReceive('put')->once()->with(__DIR__.'/Guardfile', file_get_contents(__DIR__.'/stubs/GuardFileSingleStub.rb'));

		$generate = new GuardGenerator($files, $configMock);
		$generate->guardFile(['sass'], __DIR__);
	}

	public function testGuardFileCanBeCreatedWithMultiplePlugins()
	{
		// Mock Config
		$configMock = m::mock('Illuminate\Config\Repository[get]');
		$configMock->shouldReceive('get')->andReturn('path');

		// We don't want to REALLY create files. :)
		$files =  m::mock('Illuminate\Filesystem\Filesystem[put]');

		$files->shouldReceive('put')->once()->with(__DIR__.'/Guardfile', file_get_contents(__DIR__.'/stubs/GuardFileMultipleStub.rb') );

		$generate = new GuardGenerator($files, $configMock);
		$generate->guardFile(['sass', 'coffeescript'], __DIR__);
	}

	public function testAssetFolderCanBeCreated()
	{
		// Mock Config
		$configMock = m::mock('Illuminate\Config\Repository[get]');
		$configMock->shouldReceive('get')->andReturn('path');

		// We don't want to REALLY create files. :)
		$files =  m::mock('Illuminate\Filesystem\Filesystem');

		// Should try to create a path/sass directory.
		$files->shouldReceive('makeDirectory')->once()->with('path/sass', 0777, true);

		$generate = new GuardGenerator($files, $configMock);
		$generate->assetFolder('sass');
	}

}