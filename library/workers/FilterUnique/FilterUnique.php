<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("abbr.V");
/**
 *
 * FilterUnique
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterUnique extends FilterBase{
    
    var $stock;
    
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
        foreach($items as $item) {
        	if($this->_unique($item)) {
	        	$rss20_filtered->setItem($item);
        	}
        }
        return $rss20_filtered;
    }
    
    function _unique (&$item) {
		if(!empty($this->stock)) {
		foreach($this->stock as $ref) {
			if($this->_compareFeed($item,$ref)) {
				return false;
			}
		}
		}
		$this->stock[] = $item;
		return true;
	}
    
    function _compareFeed (&$needle, &$haystack) {
    	if(!V::istype("RssItem",$haystack)) return false;
		$type = $this->variable("property");
		switch ($type) {
			case 'title':
			case 'description':
			case 'link':
				$method_name = "get".ucfirst($type);
				$value = call_user_func(array(&$needle,$method_name));
				return V::equal(call_user_func(array(&$haystack,$method_name)),$value);
			case 'all':
				foreach($needle as $key=>$var) {
					$method_name = "get".ucfirst($key);
					$value = call_user_func(array(&$needle,$method_name));
					if(!V::equal(call_user_func(array(&$haystack,$method_name)),$value)) {
						return false;
					}
				}
				return true;
		
			default:
				break;
		}
	}
    
    function description() {
    	return "重複したフィードを削除する（先に読み取られたものが残る）";
    }

	function config()
	{
		return array("property"=>array("重複を調べるプロパティ","select",array("title"=>"タイトル","description"=>"内容","link"=>"リンク","all"=>"全て")));
	}
	
	function rhacover(){
		return "1.3.0";
	}
}
?>
