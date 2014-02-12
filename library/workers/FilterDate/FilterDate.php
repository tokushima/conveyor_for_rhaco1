<?php
Rhaco::import("model.FilterBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("tag.feed.Rss20");
    
/**
 * FilterDate
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterDate extends FilterBase{
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
        if(strstr($this->variable("time"),'.')) {
        	$time = (int)($this->variable("time")*24);
	        $date = DateUtil::addHour(time(),$time*-1);
        	
        }else{
	        $date = DateUtil::addDay(time(),$this->variable("time")*-1);
        }

        foreach ($items as $item) {
            if(DateUtil::parseString($item->getPubDate()) > $date || $item->getPubDate() == ''){
	            $rss20_filtered->setItem($item);
            }
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "発行日によるフィルタを行う";
    }

	function config()
	{
		return array("time"=>"期間");
	}
	
	function rhacover(){
		return "1.1.0";
	}
}
?>
