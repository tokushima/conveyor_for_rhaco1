<?php
Rhaco::import("model.PublishBase");
Rhaco::import("tag.HtmlParser");
Rhaco::import("abbr.V");
/**
 * PlanetPublishHtml
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PlanetPublishHtml extends PublishBase{
	function execute($rss){
		$parser		= new HtmlParser(null,Rhaco::constant("WORKER_PATH"),Rhaco::constant("WORKER_URL"));
		$channel	= $rss->getChannel();
		$items = $rss->getItem();
		foreach($items as $key=>$item) {
			$item->setDescription(preg_replace('@class\s*?=\s*?\'(?:main|sidebar|content|header|box|boxtop|boxbottom)\'@','',$item->getDescription()));
			if(!method_exists($item,"getWidget")) $item = V::mixin($item,array("getWidget",'','return false;'));
			$item = V::mixin($item,array("getPageName",'$source','if(method_exists($source,"getValue")) return $source->getValue();'));
			$items[$key] = $item;
		}
		$parser->setVariable("title",$this->variable("title"));
		$parser->setVariable("description",$this->variable("description"));		
		$parser->setVariable("feedurl",Rhaco::url($this->variable("feedpath")));
		$parser->setVariable("stylesheet",dirname($this->variable("stylesheet"))."/style.css");
		$parser->setVariable("theme",$this->variable("stylesheet"));
		$parser->setVariable("feeds",$this->getFeedsFromItems($items));
		$parser->setVariable("items",$items);
		$parser->write($this->template("output.html",__FILE__));

		return $rss;
	}
	
	function getFeedsFromItems($items) {
		$results = array();
		foreach($items as $item) {
			$source = $item->getSource();
			if(V::istype("RssSource",$source) && !isset($results[$source->getUrl()])) {
				$results[$source->getUrl()] = $source->getValue();
			}
		}
		return $results;
	}
	
	function config() {
		return array(
			"title"=>"タイトル",
			"description"=>"説明",
			"feedurl"=>"RSSフィードのURL",
			"stylesheet"=>"使用するスタイルシート",
			"feeds"=>"購読したフィード"
		);
	}
	
	function description(){
		return "プラネットサイト用Htmlを出力";
	}

    function rhacover() {
    	return "1.3.0";
    }
}
?>