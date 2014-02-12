<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("tag.feed.FeedParser");
Rhaco::import("abbr.V");
/**
 * PlanetSubscriptionSetRss
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class SubscriptionRssObject extends SubscriptionBase{
	function execute($rss){
		$object = Rhaco::getVariable("subscriptionrssobject__rss");
		if(is_string($object)){
			$object = FeedParser::parse($object);
		}
		if(!V::istype("Rss20",$object)){
			return $rss;
		}
		return $this->merge($rss,$object);
	}
	function description(){
		return "内部使用プラグイン";
	}
}
?>