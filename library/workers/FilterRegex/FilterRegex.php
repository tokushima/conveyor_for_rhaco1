<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
/**
 *
 * FilterRegex
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterRegex extends FilterBase{

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
        $mode = $this->variable("mode") or ($mode = "replace");
        $property = ($this->variable("property"))? $this->variable("property") : 'description';
       	$method_name = "set".ucfirst($property);
        foreach ($items as $item) {
            if($mode==="replace") {
            	if(method_exists($item,$method_name)) {//var_dump($this->replace($property,$item));
            		call_user_func(array(&$item,$method_name),$this->replace($property, $item));
            	}
	            $rss20_filtered->setItem($item);
            }else{
	            if($search = $this->search($property, $item)) {
	            	if(method_exists($item,$method_name)) {
	            		call_user_func(array(&$item,$method_name),$search);
	            	}
	            	$rss20_filtered->setItem($item);
	            }
            }
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "正規表現抽出・置換を行う";
    }

	function config()
	{
		return array(
		"mode"=>array("動作モード","select",array("replace"=>"置換","search"=>"検索")),
		"property"=>"検索するプロパティ(指定しない場合はdescription)",
		"search"=>"検索条件(正規表現、指定しない場合は全文字列が対象)",
		"replace"=>"置換文字列",
		);
	}

    function search($property, &$item)
    {
    	$method_name = "get".ucfirst($property);
    	$message = call_user_func(array(&$item,$method_name));
    	if(is_array($message) || is_object($message)) return false;
    	$regex = ($this->variable("search"))? $this->variable("search") : "(.*)";
     	if(!preg_match('@'.$regex.'@ims', $message, $match)){
    		return false;
    	}
        return $match[0];
    }
	
	function replace ($property, &$item) {
    	$method_name = "get".ucfirst($property);
    	$message = call_user_func(array(&$item,$method_name));//var_dump($message);
    	if(is_array($message) || is_object($message)) return false;
		$regex = ($this->variable("search"))? $this->variable("search") : "(.*)";
        $replace = $this->variable("replace") or ($replace = "");
        if(preg_match_all('@\$\[([a-zA-Z]*?)\]@',$regex,$match)) {
    		$replaced = array();
    		foreach($match[1] as $property) {
    			if(!in_array($property,$replaced)) {
  					$method_name = "get".ucfirst($property);
    				$regex = preg_replace('@\$\['.$property.'\]@',call_user_func(array(&$item,$method_name)),$regex);
    				$replaced[] = $property;
    			}
    		}
    	}
    	if(preg_match_all('@\$\[([a-zA-Z]*?)\]@',$replace,$match)) {
    		$replaced = array();
    		foreach($match[1] as $property) {
    			if(!in_array($property,$replaced)) {
  					$method_name = "get".ucfirst($property);
    				$replace = preg_replace('@\$\['.$property.'\]@',call_user_func(array(&$item,$method_name)),$replace);
	    			$replaced[] = $property;
    			}
    		}
    	}
    	return preg_replace('@'.$regex.'@', $replace, $message);
	}
	
	function rhacover(){
		return "1.2.0";
	}
}
?>
