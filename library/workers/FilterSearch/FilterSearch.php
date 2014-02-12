<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
/**
 *
 * FilterSearch
 * @author SHIGETA Takeshiro
 *

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterSearch extends FilterBase{
    
    function execute($rss20)
    {
        $channel = $rss20->getChannel();
        $items =& $rss20->getItem();

        $rss20_filtered = new Rss20();
        $rss20_filtered->setChannel($channel->getTitle(),
            $channel->getDescription(),
            $channel->getLink(),
            "ja"
        );
        foreach ($items as $item) {
        	$property = ($this->variable("property"))? $this->variable("property") : 'description';
            $method_name = "get".ucfirst($property);
        	if($this->searchcount(call_user_func(array(&$item,$method_name))) >= $this->variable("number")){
	            $rss20_filtered->setItem($item);
            }
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "AND検索を行う";
    }

	function config()
	{
		return array(
		"property"=>array("検索するプロパティ","select",array("title"=>"タイトル","description"=>"内容","link"=>"リンク")),
		"word"=>array("検索ワード(改行区切り)","textarea"),
		"number"=>"指定数以上のマッチ数のみ動作",
		);
	}

    function searchcount($message)
    {
		$count = 0;
        $keys = explode("\n", $this->variable("word"));
        foreach($keys as $key) {
        	if($key == '*') {
        		$count = $this->variable("number") + 1;
        		continue;
        	}
        	elseif(substr($key,0,1) == '-') {
        		$key = substr($key,1);
 		    	if( preg_match('|'.$key.'|', $message)){
		    		return -1;
		    	}
        	}
        	elseif( preg_match('|'.$key.'|', $message)){
        		$count++;
        	}else{
        		return 0;
        	}
        }
        return $count;
    }
	
	function rhacover(){
		return "1.2.0";
	}
}
?>
