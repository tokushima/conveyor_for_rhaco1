<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
/**
 * FilterSetProperty
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterSetProperty extends FilterBase{
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
		if($this->variable("property")) {
			$properties = explode("\n",$this->variable("property"));
		}
		if($this->variable("value")) {
			$values = explode("\n",$this->variable("value"));
		}

        foreach ($items as $item) {
	    	foreach($properties as $key=>$property) {
	    		if(isset($item->$property) && !empty($values[$key])){
	    			$method_name = "set".ucfirst($property);
	    			call_user_func(array(&$item,$method_name),StringUtil::encode($values[$key]));
	    		}
	    	}
            $rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "値を設定する";
    }

	function config()
	{
		return array("property"=>array("プロパティ名","textarea","",true),"value"=>array("値","textarea","",true));
	}
		
	function rhacover(){
		return "1.2.0";
	}
}
?>
