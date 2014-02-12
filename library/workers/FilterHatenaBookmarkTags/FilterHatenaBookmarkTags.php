<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("network.http.Http");
Rhaco::import("tag.feed.Rss10");		
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("abbr.V");
/**
 * FilterHatenaBookmarkTags
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */

class FilterHatenaBookmarkTags extends FilterBase{
    
    
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
		$feed = new SimpleTag();
        foreach($items as $item) {
      		if(!method_exists($item,"getMeta")){
       			$item = V::mixin($item,V::anonym("meta",array("getMeta",'$name','if(isset($this->meta[$name])) return $this->meta[$name];'),array("setMeta",'$name,$value','$this->meta[$name]=$value;')));
       		}
        	$url = "http://b.hatena.ne.jp/entry/rss/".$item->getLink();
        	$feed->set(Http::body($url));
        	foreach($feed->getIn("dc:subject") as $bitem) {
        		$tag[] = $key = str_replace(array('*','+','#'),'',$bitem->getValue());
        		$cloud[$key]++;
        	}
        	if(is_array($tag)) {
        		$tag = array_values(array_unique($tag));
        	}
        	if($tag && $cloud) {
        		$item->setMeta("tag",$tag);
        		$item->setMeta("cloud",$cloud);
        	}
        	$rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "はてなブックマークのタグを取得する";
    }
    
	function rhacover(){
		return "1.2.0";
	}
}
?>
