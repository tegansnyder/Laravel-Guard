<?php

use Way\Console\GuardGenerator;
use Mockery as m;

class GuardGeneratorTest extends PHPUnit_Framework_TestCase {
	public function setUp()
	{
		$this->file = m::mock('Illuminate\Filesystem\Filesystem');
		$this->guardFile = m::mock('Way\Console\Guardfile');

		$this->generate = new GuardGenerator($this->file, $this->guardFile);
	}

	public function tearDown()
	{
		Mockery::close();
	}

	public function testCanCreateFolder()
	{
		$this->file->shouldReceive('exists')->andReturn(false);
		$this->file->shouldReceive('makeDirectory')->with('foo', 0777, true)->once();

		$this->generate->folder('foo');
	}

	public function testWillNotCreateFolderIfExists()
	{
		$this->file->shouldReceive('exists')->andReturn(true);
		$this->file->shouldReceive('makeDirectory')->never();

		$this->generate->folder('foo');
	}

	public function testCanCreateGuardfile()
	{
		$this->guardFile->shouldReceive('getStubs')->once();
		$this->guardFile->shouldReceive('put')->once();

		$this->generate->guardFile(['foo']);
	}

	public function testCanCreateLogFileForPlugins()
	{
		$plugins = ['foo'];
		$this->file->shouldReceive('exists')->andReturn(true);
		$this->file->shouldReceive('put')->with('some/path/plugins.txt', json_encode($plugins))->once();

		$this->generate->log($plugins, 'some/path');
	}
}