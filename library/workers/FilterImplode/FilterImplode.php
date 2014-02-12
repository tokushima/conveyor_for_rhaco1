<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("tag.model.TemplateFormatter");

/**
 * FilterSummarySimple
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterImplode extends FilterBase{    
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
        	$description = $description.strip_tags(TemplateFormatter::unescape(TemplateFormatter::getCdata($item->getDescription())));
        }
       	$item->setAuthor("SHIGETA Takeshiro");
       	$item->setTitle("全文まとめ");
       	$item->setLink("");
       	$item->setPubDate(time());
       	$item->setDescription($description);
       	$rss20_filtered->setItem($item);
        return $rss20_filtered;
    }
    
    function config () {
	}
    
    function description() {
    	return "文章をGetSenを使ってまとめる。";
    }

	function rhacover(){
		return "1.2.0";
	}
}
?>
