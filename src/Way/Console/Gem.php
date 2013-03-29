<?php namespace Way\Console;

class Gem {

	public function mustBeAvailable($gemName)
	{
		if (! $this->exists($gemName))
		{
			$this->install('guard');
		}
	}

	public function exists($name)
	{
		return ! is_null(shell_exec("gem spec $name 2>/dev/null"));
	}

	public function install($name)
	{
		shell_exec("gem install $name");
	}

}