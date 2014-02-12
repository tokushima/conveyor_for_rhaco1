<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("io.FileUtil");
/**
 * SubscriptionFilelist
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
*/
class SubscriptionFilelist extends SubscriptionBase{
	function execute($rss){
		$urls = array();
		$newrss = new Rss20();
		$newrss->setChannel("Filelist");
		$filer = new FileUtil();
		$path = $this->variable("path");
		foreach($filer->ls($path,$this->variable("recursive")) as $file){
			if(!$this->variable("extension") || ($file->getExtension()!=="." && stristr($this->variable("extension"),$file->getExtension())!==false)) {
			$item = new RssItem20();
			$item->setTitle($file->getOriginalName());
			$item->setPubDate($file->getUpdate());
			$item->setLink($file->getFullName());
			$newrss->setItem($item);
			}
		}
		return $this->merge($rss,$newrss);
	}
	function description(){
		return "ファイルリストを取得する";
	}
	function config(){
		return array(
				"path"=>array("ファイルリストを取得するディレクトリ","text",Rhaco::path()),
				"recursive"=>array("ファイルリストを再帰的に取得するか?","select",array(1=>"する",0=>"しない")),
				"extension"=>array("拡張子")
				);
	}
	function rhacover(){
		return "1.2.0";
	}
}
?>