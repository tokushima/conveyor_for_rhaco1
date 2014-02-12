<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("Xmlrpc");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("abbr.V");
/**
 *
 * FilterHatenaBookmarkUserCount
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */

class FilterHatenaBookmarkUserCount extends FilterBase{
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
        $rpc = new XmlrpcClient("http://b.hatena.ne.jp/xmlrpc");
        $tag = new SimpleTag();
        $rpc->setMethod("bookmark.getCount");
        foreach($items as $item) {
        	$rpc->push($item->link);
        }
        $tag->set($rpc->request());
       $result = array();
       foreach($tag->getIn("member") as $member) {
       	$results[$member->getInValue("name")] = $member->getInValue("int");
       }
		foreach($items as $key=>$item) {
      		if(!method_exists($item,"getMeta")){
       			$item = V::mixin($item,V::anonym("meta",array("getMeta",'$name','if(isset($this->meta[$name])) return $this->meta[$name];'),array("setMeta",'$name,$value','$this->meta[$name]=$value;')));
       		}
			$item->setMeta("hatenabookmark_users",$results[$item->getLink()]);
			$item->setMeta("hatenabookmark_url","http://b.hatena.ne.jp/entry/".$item->getLink());
			$rss20_filtered->setItem($item);
		}
        return $rss20_filtered;
    }
    
    function description() {
    	return "はてなブックマークの数をカウントする";
    }

	function required () {
		return array(
			"Xmlrpc"=>""
		);
	}	
	
	function rhacover(){
		return "1.2.0";
	}
}
?>
