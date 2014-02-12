<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.StringUtil");
Rhaco::import("network.http.Browser");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("tag.feed.Rss20");
/**
 * CustomFeedJavascriptNews
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class CustomfeedJavascriptNews extends SubscriptionBase{
    function execute($variable){
    	/***
    	 * $worker = new CustomfeedJavascriptNews();
    	 * $rss = $worker->execute(new Rss20());
    	 * $channel = $rss->getChannel();
    	 * eq("Javascript News",$channel->getTitle());
    	 * $items = $rss->getItem();
    	 * $item = $items[0];
    	 * $source = $item->getSource();
    	 * eq("http://javascriptist.net/docs/news.html",$source->getUrl());
    	 * eq("Javascript News",$source->getValue());
    	 *     	 
    	 */
        $rss20 = new Rss20();
        $rss20->setChannel("Javascript News",
            "description",
            "http://javascriptist.net/docs/news.html",
            "ja"
        );
		$source = new RssSource("http://javascriptist.net/docs/news.html","Javascript News");
		$browser = new Browser();
		$pubdate = array();
    	$page = StringUtil::encode($browser->get("http://javascriptist.net/docs/news.html"));
		$tag = new SimpleTag();
		$tag->set($page);
		foreach($tag->getIn("ul") as $key => $ul_tag){
			if($ul_tag->getParameter("class") == "recent_list") {
    			foreach($ul_tag->getIn("li") as $li_tag) {
    				$a_tags = $li_tag->getIn("a");
    				$a_tag = $a_tags[0];
    				$title = $a_tag->getValue();
    				$link = $a_tag->getParameter("href");
    				$description = $a_tag->getParameter("title");
					$item = new RssItem20();
	    			$item->setTitle($title);
	    			$item->setLink($link);
	    			$item->setDescription($description);
	    			$item->setPubDate(time());
	    			$item->setSource($source);
	    			$rss20->setItem($item);
    			}
			}
		}
		$rss20 = $this->merge($variable,$rss20);
        return $rss20;
    }
    function description(){
    	return "JavascriptNewsを取得する";
    }
    function rhacover() {
    	return "1.6.1";
    }
    function extraTests(&$assert){
    	$rss = $this->execute(new Rss20());
    	$channel = $rss->getChannel();
    	$items = $rss->getItem();
    	$assert->assertEquals($channel->getTitle(),"Javascript News");
    	$assert->assertEquals($channel->getLink(),"http://javascriptist.net/docs/news.html");
    	//TODO:itemに関するテスト
    }
}

?>
