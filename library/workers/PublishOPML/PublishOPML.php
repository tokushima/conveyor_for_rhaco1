<?php
Rhaco::import("model.PublishBase");
Rhaco::import("tag.feed.Opml");
Rhaco::import("io.FileUtil");
Rhaco::import("lang.StringUtil");
/**
 * PublishOPML
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PublishOPML extends PublishBase{
	function execute($rss){
		$items = $rss->getItem();
		$newopml = new Opml();
		$outlines = array();
		foreach($items as $item) {
			$outline = new OpmlOutline();
			$outline->setType("rss");
			$outline->setTitle(TemplateFormatter::getCdata($item->getTitle()));
			$outline->setXmlUrl($item->getLink());
			$outline->setText(TemplateFormatter::getCdata($item->getDescription()));
			$newopml->setOutline($outline);
		}
		if($this->variable("path")) {
			$filer = new FileUtil();
			$filer->write(Rhaco::path($this->variable("path")),"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n".StringUtil::encode($newopml->get()));
		}else{
			echo $newopml->output();
		}
		return $rss;
	}
	function description(){
		return "Output RSS to OPML file";
	}
	
	function config(){
		return array("path"=>"output file path");
	}
}
?>