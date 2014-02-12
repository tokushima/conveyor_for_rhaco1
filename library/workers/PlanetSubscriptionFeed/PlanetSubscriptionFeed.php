<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("lang.Variable");
Rhaco::import("tag.feed.FeedParser");
Rhaco::import("tag.feed.model.RssSource");
/**
 * PlanetSubscriptionFeed
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PlanetSubscriptionFeed extends SubscriptionBase{
	function execute($rss){
		$urls = array();
		$newrss = new Rss20();

		foreach(explode("\n",$this->variable("url")) as $url){
			$url = trim($url);

			if(!empty($url)){
				$urls[] = $url;
			}
		}
		if(sizeof($urls) < 1){
			return $rss;
		}else{
			foreach($urls as $url) {
				$rss_read = FeedParser::read($url);
				$channel = $rss_read->getChannel();
				$title = $channel->getTitle();
				$items = $rss_read->getItem();
				$link = $channel->getLink();
				if(Variable::istype('atomlink',$link)) {
					$link = $link->href;
				}
				$source = new RssSource($url,$title);
				foreach($items as $item){
					$item->setSource($source);
					$newrss->setItem($item);
				}
			}
		}
		return $this->merge($rss,$newrss);
	}
	function description(){
		return "Feedを取得する(Planetサイト用)";
	}
	function config(){
		return array(
				"url"=>array("RSSのURL","textarea")
				);
	}
	function rhacover() {
    	return "1.3.0";
    }
}
?>