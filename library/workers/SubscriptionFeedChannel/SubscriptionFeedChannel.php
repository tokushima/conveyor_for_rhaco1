<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("tag.feed.FeedParser");
/**
 * FeedIn
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class SubscriptionFeedChannel extends SubscriptionBase{
	function execute($rss){
		$urls = array();
		$newrss = new Rss20();

		foreach(explode("\n",$this->variable("url")) as $url){
			$url = trim($url);

			if(!empty($url)){
				$urls[] = $url;
			}
		}
		foreach($urls as $url){
			$item = new RssItem20();
			$newrss = FeedParser::read($url);
			$channel = $newrss->getChannel();
			$item->setTitle($channel->getTitle());
			$item->setLink($url);
			$item->setDescription($channel->getDescription());
			$item->setGuid($channel->getLink());
			$rss->setItem($item);
		}
		return $rss;
	}
	function description(){
		return "Feed Channelを取得する";
	}
	function config(){
		return array(
				"url"=>array("RSSのURL","textarea"),
				);
	}
	
	function rhacover() {
		return "1.2.0";
	}
}
?>