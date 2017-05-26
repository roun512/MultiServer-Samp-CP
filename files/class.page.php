<?php
/**
* @author roun512
*/
class page
{
	public $title,
		$css = array("main.css"),
		$js = array("main.js");


	public function css($file) {
		array_push($this->css, $file);
	}

	public function js($file) {
		array_push($this->js, $file);
	}
}
?>