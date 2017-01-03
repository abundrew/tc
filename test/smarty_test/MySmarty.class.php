<?php

require(dirname(__FILE__).'/smarty/Smarty.class.php');

class MySmarty extends Smarty
{
	function MySmarty()
	{
		$this->Smarty();

		$this->template_dir = dirname(__FILE__).'/template/';
		$this->compile_dir = dirname(__FILE__).'/smarty/compile/';
		$this->config_dir = dirname(__FILE__).'/smarty/configs/';
		$this->cache_dir = dirname(__FILE__).'/smarty/cache/';

        $this->caching = true;
	}

}

?>
