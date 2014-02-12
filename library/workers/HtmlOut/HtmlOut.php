<?php
Rhaco::import("model.PublishBase");
Rhaco::import("tag.HtmlParser");

class HtmlOut extends PublishBase{
	function HtmlOut(){
		$this->__init__(__FILE__);
	}
	function execute($rss){
		$parser		= new HtmlParser();
		$channel	= $rss->getChannel();
		$parser->setVariable("title",$channel->getTitle());
		$parser->setVariable("description",$channel->getDescription());		
		$parser->setVariable("items",$rss->getItem());
		$parser->write($this->template("output.html",__FILE__));

		return $rss;
	}
	function description(){
		return "Htmlを出力";
	}
}
?>