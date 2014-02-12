<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("tag.feed.FeedParser");
/**
 * FeedIn
 * @author TOKUSHIMA Kazutaka
 */
class FeedIn extends SubscriptionBase{
	function execute($variable){
		$urls = array();
		$newrss = new Rss20();

		foreach(explode("\n",$this->variable("url")) as $url){
			$url = trim($url);

			if(!empty($url)){
				$urls[] = $url;
			}
		}
		if(sizeof($urls) > 1){
			$items = FeedParser::getItem($urls);
			$newrss->setChannel("Mix in Rss\n".implode("\n",$urls));
			foreach($items as $item){
				$newrss->setItem($item);
			}
		}else if(sizeof($urls) == 1){
			$newrss = FeedParser::read($urls[0],null,array('ACCEPT_LANGUAGE'=>$_SERVER['HTTP_ACCEPT_LANGUAGE']));
		}
		return $this->merge($variable,$newrss);
	}
	function description(){
		return "Feedを取得する";
	}
	function config(){
		return array(
				"url"=>array("RSSのURL","textarea"),
				);
	}
}
?>