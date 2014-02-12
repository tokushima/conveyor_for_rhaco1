<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("tag.feed.FeedParser");
/**
 *
 * SubscriptionSearchengine
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class SubscriptionSearchengine extends SubscriptionBase{
	function execute($variable){
		$urls = array();
		$newrss = new Rss20();

		foreach(explode("\n",$this->variable("engines")) as $url){
			$url = trim($url);

			if(!empty($url)){
				$newrss = FeedParser::read(str_replace("<keyword>",$this->variable("keyword"),$url));
				$variable = $this->merge($variable,$newrss);
			}
		}
		return $variable;
	}
	function description(){
		return "Feedを取得する";
	}
	function config(){
		return array(
				"engines"=>array("検索エンジンの検索URL","textarea"),
				"keyword"=>array("キーワード","text"),
				);
	}
	function rhacover(){
		return "1.2.0";
	}
}
?>