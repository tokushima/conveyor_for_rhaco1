<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("lang.StringUtil");
Rhaco::import("network.http.Browser");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("tag.feed.Rss20");
/**
 * CustomFeedEconomicGuidepost
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class CustomfeedEconomicGuidepost extends SubscriptionBase{
    function execute($variable){
        $rss20 = new Rss20();
        $rss20->setChannel("Japanese Economical Guidepost",
            "description",
            "http://www5.cao.go.jp/keizai3/shihyo/",
            "ja"
        );
		$source = new RssSource("http://www5.cao.go.jp/keizai3/shihyo/","今週の指標");
		$browser = new Browser();
    	$page = StringUtil::encode($browser->get("http://www5.cao.go.jp/keizai3/shihyo/"));
		$tag = new SimpleTag();
		$tag->set($page);
		$flag = false;
		$count = 0;
		foreach($tag->getIn("ul") as $outerdiv){
			foreach($outerdiv->getIn("a") as $innerdiv){
				$item = new RssItem20();
				$link = $innerdiv->getParameter("href");
				$datearray = array(substr($link,0,4),substr($link,5,2),substr($link,7,2));
				$dateint = strtotime(implode($datearray));
				$title = StringUtil::encode($innerdiv->getValue());
    			$description = '';
    			$item->setTitle($title);
    			$item->setLink($link);
    			$item->setDescription($description);
    			$item->setPubDate($dateint);
    			$item->setSource($source);
    			$rss20->setItem($item);
			}
			if($count++ > 10) break;
				
		}
		$rss20 = $this->merge($variable,$rss20);
        return $rss20;
    }

    
    function description(){
    	return "今週の指標を取得する";
    }

    function rhacover() {
    	return "1.1.0";
    }
}

?>
