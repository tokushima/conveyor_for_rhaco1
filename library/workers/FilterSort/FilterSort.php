<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
/**
 * FilterSort
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterSort extends FilterBase{
    
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
        if($this->variable("order") == "ascend") {
        	usort($items,array($this, "ascend"));
        }elseif($this->variable("order") == "descend") {
        	usort($items,array($this, "descend"));
        }else{
        	return $rss20;
        }
        if(!empty($items)) {
	        foreach($items as $item) {
	        	$rss20_filtered->setItem($item);
	        }
        }
        return $rss20_filtered;
    }
    
    function ascend ($a, $b) {
    	$property = $this->variable("property");
    	$method_name = "get".ucfirst($property);
        $al = strtolower(call_user_func(array(&$a,$method_name)));
        $bl = strtolower(call_user_func(array(&$b,$method_name)));
		if($property==='pubDate'){
			$al = DateUtil::parseString($al);
			$bl = DateUtil::parseString($bl);
		}
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
	}
    
    function descend ($a, $b) {
    	$property = $this->variable("property");
    	$method_name = "get".ucfirst($property);
        $al = strtolower(call_user_func(array(&$a,$method_name)));
        $bl = strtolower(call_user_func(array(&$b,$method_name)));
		if($property==='pubDate'){
			$al = DateUtil::parseString($al);
			$bl = DateUtil::parseString($bl);
		}
        if ($al == $bl) {
            return 0;
        }
        return ($al < $bl) ? +1 : -1;
	}
    
    function description() {
    	return "フィードをソートする";
    }

	function config()
	{
		return array("property"=>array("ソートするプロパティ名","select",array("title"=>"タイトル","author"=>"著者","category"=>"カテゴリ","description"=>"詳細","comments"=>"コメント","pubDate"=>"日時")),"order"=>array("オーダー順","select",array("ascend"=>"昇順","descend"=>"降順")));
	}
	
	function rhacover(){
		return "1.2.0";
	}
}
?>
