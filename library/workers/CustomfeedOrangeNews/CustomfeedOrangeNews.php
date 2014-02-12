<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("lang.StringUtil");
Rhaco::import("network.http.Browser");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("tag.feed.Rss20");
/**
 * CustomFeedOrangeNews
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class CustomfeedOrangeNews extends SubscriptionBase{
    function execute($variable){
        $rss20 = new Rss20();
        $rss20->setChannel("Orange News Tech",
            "Orange News Tech Part",
            "http://secure.ddo.jp/~kaku/tdiary/",
            "ja"
        );
        $source = new RssSource("http://secure.ddo.jp/~kaku/tdiary/","Orange News Tech");

		$browser = new Browser();
    	$page = $browser->get("http://secure.ddo.jp/~kaku/tdiary/");
		$tag = new SimpleTag();
		$tag->set($page);
		$flag = false;
		foreach($tag->getIn("div") as $outerdiv){
			foreach($outerdiv->getIn("div") as $innerdiv){
			if($innerdiv->getParameter("class") == 'day') {
				$datetag = $innerdiv->getInValue("span");
				preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$datetag,$datearray);
			}
				foreach($innerdiv->getIn("div") as $innerdiv2) {
					foreach($innerdiv2->getIn("div") as $innerdiv3) {
					if($innerdiv3->getInValue("h2")) {
						$title = StringUtil::encode($datearray[0]);
						$flag = !$flag;
						continue;
					}
					if($flag){
						$item = new RssItem20();
						$link = '';
						$about = '';
						$description =  StringUtil::encode($innerdiv3->value);
						$item->setTitle($title);
						$item->setLink($link);
						$item->setDescription($description);
						$item->setPubDate($title);
						$item->setSource($source);				
            			$rss20->setItem($item);
					}
					
					}
					
				}
			}	
		}
		$rss20 = $this->merge($variable,$rss20);
        return $rss20;
    }
    function description(){
    	return "Orange News の技術ネタを取得します";
    }
    function rhacover() {
    	return "1.1.0";
    }
}

?>
