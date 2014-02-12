<?php
Rhaco::import("lang.DateUtil");
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
/**
 * FilterMoveDate
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterMoveDate extends FilterBase{
    
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

        foreach ($items as $item) {
	            $item->setPubDate(DateUtil::add($item->pubDate,0,0,0,$this->variable("day"),$this->variable("month"),$this->variable("year")));
	            $rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "発行日を指定年月日分移動する";
    }

	function config()
	{
		return array("year"=>"年","month"=>"月","day"=>"日");
	}
	
	function rhacover(){
		return "1.2.0";
	}
}
?>
