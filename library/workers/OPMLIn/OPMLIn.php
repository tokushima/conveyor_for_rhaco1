<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("tag.feed.Opml");
Rhaco::import("io.Cache");

class OPMLIn extends SubscriptionBase{
	function execute($rss){
//		return FeedParser::read(Rhaco::path($this->variable("path")));
		return $this->merge($rss,$this->read(Rhaco::path($this->variable("path"))));
	}
	function description(){
		return "OPMLファイルから取得する";
	}
	function config(){
		return array(
				"path"=>"OPMLファイルへのパス",
				"urltype"=>array("取得するURLのタイプ","select",array("xml"=>"XML","html"=>"HTML"))
				);
	}

	function read($url,$time=null){
		$src = "";
		if(!Rhaco::constant("FEED_CACHE") || (Rhaco::constant("FEED_CACHE_TIME") > 0 && Cache::isExpiry($url,Rhaco::constant("FEED_CACHE_TIME")))){
			$src = (preg_match("/[\w]+:\/\/[\w]+/",$url)) ? (($time > 0) ? Http::modified($url,$time) : Http::body($url,"GET",$headers)) : File::read($url);
			if(Rhaco::constant("FEED_CACHE")) Cache::set($url,$src);
		}else{
			$src = Cache::get($url);
		}
		Logger::debug(Message::_("read feed [{1}]",$url));
		return $this->parse(StringUtil::encode($src));
	}
	
	function parse($src){
		$toFeed = new Rss20();
		$fromFeed = new Opml();
		if($fromFeed->set($src)){
			$toFeed->setChannel($fromFeed->getTitle(),$fromFeed->getTitle());
			if($this->variable("urltype")==="xml") {
				foreach($fromFeed->getXmlOutlines() as $outline){
					$item = new RssItem20($outline->getTitle(),$outline->getDescription(),$outline->getXmlUrl());
					$item->setPubDate(time());
					$item->setComments($outline->getValue());
					$item->setCategory($outline->getTags());
					$toFeed->setItem($item);
				}
			}else{
				foreach($fromFeed->getHtmlOutlines() as $outline){
					$item = new RssItem20($outline->getTitle(),$outline->getDescription(),$outline->getHtmlUrl());
					$item->setPubDate(time());
					$item->setComments($outline->getValue());
					$item->setCategory($outline->getTags());
					$toFeed->setItem($item);
				}
			}
		}
		return $toFeed;
	}

}
?>