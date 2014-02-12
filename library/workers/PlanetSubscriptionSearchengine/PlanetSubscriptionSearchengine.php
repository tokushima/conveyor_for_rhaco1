<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.StringUtil");
Rhaco::import("tag.feed.FeedParser");
/**
 * PlanetSubscriptionSearchengine
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PlanetSubscriptionSearchengine extends SubscriptionBase{
	function execute($rss){
		$yaml = Spyc::YAMLLoad(dirname(__FILE__)."/".$this->variable("file"));
		$urls = array();
		$newrss = new Rss20();

		foreach($yaml['engines'] as $url){
			$url = trim($url);

			if(!empty($url)){
				$url = str_replace("<keyword>",urlencode($this->variable("keyword")),$url);
				$url = str_replace("<keyword:euc-jp>",urlencode(StringUtil::encode($this->variable("keyword"),"EUC-JP")),$url);				
				$read_rss = FeedParser::read($url);
				$channel = $read_rss->getChannel();
				$link = $channel->getLink();
				if(is_a($link,'atomlink')) {
					$link = $link->href;
				}
				$source = new RssSource($url,$channel->getTitle());
				$newrss = new Rss20();
				$newrss->setChannel("");
				foreach($read_rss->getItem() as $item){
					$item->setSource($source);
					$newrss->setItem($item);
				}
				$rss = $this->merge($rss,$newrss);
			}
		}
		return $rss;
	}
	function description(){
		return "検索エンジンからFeedを取得する(Planetサイト用)";
	}
	function config(){
		return array(
				"file"=>array("検索エンジンリスト（yamlファイル）","text"),
				"keyword"=>array("検索キーワード","text"),
				);
	}
	function rhacover() {
    	return "1.3.0";
    }
}
?>