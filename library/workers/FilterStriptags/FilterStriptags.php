<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("tag.model.TemplateFormatter");
/**
 * FilterStriptags
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterStriptags extends FilterBase{
    
    function execute($rss20)
    {
        $channel = $rss20->getChannel();
        $items = $rss20->getItem();
        $properies = explode("\n",$this->variable("property"));
        $allow_tags = "<".implode("><",$this->variable("allow")).">";

        $rss20_filtered = new Rss20();
        $rss20_filtered->setChannel($channel->getTitle(),
            $channel->getDescription(),
            $channel->getLink(),
            "ja"
        );
        if(empty($properies)) {
        	return $rss20;
        }
        foreach ($items as $key=>$item) {
        	foreach($properies as $property) {
        		$item->$property = strip_tags(TemplateFormatter::getCdata($item->$property));
        	}
            $rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "タグを削除する";
    }

	function config()
	{
		return array("property"=>array("プロパティ","textarea","title\nlink"),
		"allow"=>array("allow tags","textarea","p\ndiv\na\nspan\n"));
	}
	
	function rhacover(){
		return "1.2.0";
	}
}
?>
