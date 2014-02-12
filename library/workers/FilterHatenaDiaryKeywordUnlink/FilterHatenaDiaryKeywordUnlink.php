<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");

/**
 * FilterHatenaDiaryKeywordUnlink
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterHatenaDiaryKeywordUnlink extends FilterBase{
    
    
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
        foreach($items as $key=>$item) {
        	$description = preg_replace('@<a (?:class="o?keyword"\s*)?href="http://(?:(?:d|[\w\-]+\.g)\.hatena\.ne\.jp|anond\.hatelabo\.jp)/keyword/.*?"(?:\s*class="keyword")?[^>]*>(.*?)</a>@','$1',$item->getDescription);
        	$item->setDescription($description);
			$rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "はてなキーワードを削除する";
    }
    	
	function rhacover(){
		return "1.2.0";
	}
}
?>
