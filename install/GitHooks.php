<?php

class GitHooks
	extends InstallerBase
{
	private $preCommit 		= "../.git/hooks/pre-commit";
	private $postCheckout 	= "../.git/hooks/post-checkout";
	
	public function IsInstalled()
	{
		return file_exists($this->preCommit) && file_exists($this->postCheckout);
	}
	
	public function Install()
	{
		$path = exec('echo $PATH');
		$paths = explode(PATH_SEPARATOR, $path);
		
		$path = in_array(PHP_BINDIR, $paths) ? null : PHP_BINDIR;
		
		file_put_contents($this->preCommit, $this->origPreCommit($path));
		file_put_contents($this->postCheckout, $this->origPostCheckout($path));
		
		chmod($this->preCommit, 0755);
		chmod($this->postCheckout, 0755);
	}
	
	private function origPreCommit($path)
	{
		$rows 	= array();
		$rows[] = '#!/bin/bash';
	
		if(!is_null($path))
		{	$rows[] = 'export PATH=' . $path . ':$PATH';	}
		
		$rows[] = 'source bin/hooks/pre-commit.sh';
		$rows[] = 'exit 0';
		
		return implode(PHP_EOL, $rows);
	}
	
	private function origPostCheckout($path)
	{
		$rows 	= array();
		$rows[] = '#!/bin/bash';
		
		if(!is_null($path))
		{	$rows[] = 'export PATH=' . $path . ':$PATH';	}
		
		$rows[] = 'source bin/hooks/post-checkout.sh';
		$rows[] = 'exit 0';
		
		return implode(PHP_EOL, $rows);
	}
	
	public function RenderForm()
	{}
	
}