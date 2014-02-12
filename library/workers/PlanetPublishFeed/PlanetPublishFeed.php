<?php
Rhaco::import("model.PublishBase");
Rhaco::import("io.FileUtil");
Rhaco::import("lang.StringUtil");
/**
 * PlanetPublishFeed
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PlanetPublishFeed extends PublishBase{
	function execute($rss){
		if($this->variable("title")) {
			$channel =& $rss->getChannel();
			$channel->setTitle($this->variable("title"));
			$channel->setDescription($this->variable("description"));
			$channel->setLink($this->variable("url"));
		}
		if(!$this->variable("dir")) {
			$rss->output();
		}else{
			$file = new FileUtil();
			$src = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n".StringUtil::encode($rss->get());
			$file->write($this->variable("dir"),$src);
		}
		return $rss;
	}
	function description(){
		return "Planetサイト用Feedを出力";
	}
	function config () {
		return array("title"=>"タイトル","description"=>"説明",
		"dir"=>"書き出すディレクトリ","url"=>"ページのURL");
	}
	function rhacover() {
    	return "1.3.0";
    }
}
?>