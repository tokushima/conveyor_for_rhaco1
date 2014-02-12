<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("tag.feed.FeedParser");

/**
 * SubscriptionOPMLFeed
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class SubscriptionOPMLFeed extends SubscriptionBase{
	function execute($rss){
		$result = $this->read(dirname(__FILE__).'/'.$this->variable("file"));
		$channel = $result->getChannel();
		$time = $this->variable("time")*-1;
		$items = $result->getItem();
		if(empty($rss)) {
        $rss = new Rss20();
        $rss->setChannel($channel->getTitle(),
            $channel->getDescription(),
            $channel->getLink(),
            "ja"
        );
		}
		foreach($items as $item) {
			if($time <> 0) {
				$merge_rss = FeedParser::read($item->getLink(),DateUtil::addDay(time(),$time));
			}else{
				$merge_rss = FeedParser::read($item->getLink());
			}
			$channel = $merge_rss->getChannel();
			$source = new RssSource($item->getLink(),$channel->getTitle());
			$newrss = new Rss20();
			$newrss->setChannel("");
			foreach($merge_rss->getItem() as $item){
				$item->setSource($source);
				$newrss->setItem($item);
			}
			$rss = $this->merge($rss,$newrss);
		}
		$rss->setChannel();
		return $rss;
	}
	function description(){
		return "OPMLファイルからフィードを取得する";
	}
	function config(){
		return array(
				"file"=>"OPMLファイル名",
				);
	}
	function read($url,$time=null){
		$src = "";
		if(!Rhaco::constant("FEED_CACHE",false) || Cache::isExpiry($url,Rhaco::constant("FEED_CACHE_TIME",10800))){
			if(preg_match("/[\w]+:\/\/[\w]+/",$url)){
				$src = ($time > 0) ? Http::modified($url,$time) : Http::body($url);
			}else{
				$src = File::read($url);
			}
			if(Rhaco::constant("FEED_CACHE")){
				Cache::set($url,$src);
			}
		}else{
			$src	= Cache::get($url);
		}
		return $this->parse(StringUtil::encode($src,"EUC-JP"));
	}
	function parse($src){
		$toFeed = new Rss20();

		$fromFeed = new Rss20();
		if($fromFeed->set($src)){
			return $fromFeed;
		}
		require_once((defined("RHACO_DIR")?constant("RHACO_DIR"):"")."tag/feed/Opml.php");
		$fromFeed = new Opml();
		if($fromFeed->set($src)){
			$toFeed->setChannel($fromFeed->getTitle(),$fromFeed->getTitle());
			$outlines = $fromFeed->getOutline();
			foreach($outlines[0]->outlineList as $outline){
				$item = new RssItem20($outline->getTitle(),$outline->getDescription(),$outline->getXmlUrl());
				$item->setComments($outline->getValue());
				$item->setCategory($outline->getTags());
				$toFeed->setItem($item);
			}
			return $toFeed;
		}
		return new Rss20();
	}
	function rhacover() {
    	return "1.3.0";
    }
}
?>