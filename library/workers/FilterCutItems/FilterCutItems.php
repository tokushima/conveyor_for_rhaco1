<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
/**
 * FilterCutItems
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterCutItems extends FilterBase{
    
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
        $from = $this->variable("from") or ($from = 0);
        if($to = $this->variable("to")) {
			$items = array_slice($items,$from,$to);
        }else{
        	$items = array_slice($items,$from);
        }
        foreach($items as $item) {
        	$rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "フィードの一部を削除する";
    }

	function config()
	{
		return array("from"=>"何個目のフィードから取得するか(先頭は0、負の数を指定した場合は下から数えた番号)",
						"to"=>"何個目のフィードまでを取得するか指定。指定しない場合は最後まで");
	}

	function rhacover(){
		return "1.1.0";
	}
	
}
?>
