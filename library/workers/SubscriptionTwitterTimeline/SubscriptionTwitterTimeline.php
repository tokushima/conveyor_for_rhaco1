<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("network.services.TwitterAPI.php");
/**
 *
 * SubscriptionSearchengine
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class SubscriptionTwitterTimeline extends SubscriptionBase{
	function execute($rss){
		$twitter = new TwitterAPI($this->variable("id"),$this->variable("pass"));
		$urls = array();
		$newrss = new Rss20();
		$uid = (!trim($this->variable("uid")))? null : $this->variable("uid");
		switch ($this->variable("timeline")){
			case 'public':
				$timeline = $twitter->status_public_timeline();
				break;
			case 'my':
				$timeline = $twitter->status_user_timeline($uid);
				break;
			case 'friend':
				$timeline = $twitter->status_friends_timeline($uid);
				break;
		}
//		var_dump($timeline);
		foreach( $timeline as $post){
			$item = new RssItem20();//created_at id text user
			$item->setTitle($post['user']['screen_name']);
			$item->setLink($post['user']['url']);
			$item->setPubDate(preg_replace('@[\+\-]\d+?\s@','',$post['created_at']));
			$item->setGuid($post['id']);
			$item->setDescription($post['text']);
			$item->setAuthor($post['user']['name']);
//			var_dump($post,$item);
			$newrss->setItem($item);
		}
		return $this->merge($rss,$newrss);
	}
	function description(){
		return "Twitterのタイムラインを取得する";
	}
	function config(){
		return array(
				"id"=>array("ID","text"),
				"pass"=>array("pass","password"),
				"uid"=>array("friend user id","text"),
				"timeline"=>array("timeline","select",array("public"=>"public timeline","my"=>"user timeline","friend"=>"friends timeline"))
				);
	}
	function rhacover(){
		return "1.4.1";
	}
}
?>