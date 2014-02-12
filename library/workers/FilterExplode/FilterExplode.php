<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("tag.feed.FeedParser");

/**
 * FilterSummarySimple
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterExplode extends FilterBase{    
    function execute($rss20)
    {
        $channel = $rss20->getChannel();
        $items = $rss20->getItem();

        $rss20_filtered = new Rss20();
        $rss20_filtered->setChannel($channel->getTitle(),
            $channel->getDescription(),
            $channel->getLink(),
            "ja"
        );
        $description = "";
        foreach($items as $item) {
        	$newrss = FeedParser::read($item->getLink());
       		$newitems = $newrss->getItem();
       		foreach($newitems as $newitem) {
       			$newitem->setSource(new RssSource($item->getLink(),$item->getTitle()));
       			$rss20_filtered->setItem($newitem);
       		}
        }
        return $rss20_filtered;
    }
    
    function config () {
	}
    
    function description() {
    	return "リンクがrssフィードの場合、取得する";
    }

	function rhacover(){
		return "1.2.0";
	}
}
?>
