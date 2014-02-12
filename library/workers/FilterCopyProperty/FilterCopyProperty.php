<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");

/**
 * FilterCopyProperty
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterCopyProperty extends FilterBase{
    
    
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
		if($this->variable("from")) {
			$froms = explode("\n",$this->variable("from"));
		}
		if($this->variable("to")) {
			$tos = explode("\n",$this->variable("to"));
		}

        foreach ($items as $item) {
	    	foreach($froms as $key=>$from) {
	    		if(isset($item->$from) && !empty($tos[$key])){
	    			$property = "get".ucfirst($from);
	    			$value = call_user_func(array(&$item,$property));
	    			$property = "set".ucfirst($tos[$key]);
	    			call_user_func(array(&$item,$property),StringUtil::encode($value));
	    		}
	    	}
            $rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "属性を設定する";
    }

	function config()
	{
		return array(
		"from"=>array("コピー元プロパティ名","textarea"),
		"to"=>array("コピー先プロパティ名","textarea")
		);
	}

	function rhacover(){
		return "1.2.0";
	}
	
}
?>
