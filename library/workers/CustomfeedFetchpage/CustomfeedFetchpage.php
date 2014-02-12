<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("network.http.Http");
Rhaco::import("lang.StringUtil");
Rhaco::import("io.FileUtil");
/**
 * CustomfeedFetchpage
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 * @version 0.1.0
 */
class CustomfeedFetchpage extends SubscriptionBase{
    
	function CustomfeedFetchpage(){
		$this->__init__(__FILE__);
	}
	
    function execute($rss)
    {
        $rss20 = new Rss20();
   		$link = $this->variable("handle");
   		if($link[0] == '/'){
   			$filer = new FileUtil();
   			$dpage = $filer->read($link);
   		}else{
   			$dpage = Http::body($link);
   		}
  		if(preg_match('@content=.*?charset=([a-zA-Z0-9\-\_]+).*?>@i',$dpage,$charset)) {
   			$page = mb_convert_encoding($dpage,"UTF-8",$charset[1]);
   		}else{
    		$page = StringUtil::encode($dpage);
   		}
		$match = $this->getRegex($page,$this->variable("extract_from")."(.*?)".$this->variable("extract_to"));
   		if(!empty($match)){
	   		if(strstr($match,$this->variable("delimiter"))!==false){
	       		foreach(explode($this->variable("delimiter"),$match) as $split){
	   				$item = new RssItem20();
	       			$item->setDescription($this->variable("delimiter").$split);
	           		$rss20->setItem($item);
	       		}
	   		}else{
	   			$item = new RssItem20();
	   			$item->setDescription($match);
	   			$rss20->setItem($item);
	   		}
   		}
        return $this->merge($rss,$rss20);
    }
    
    function description() {
    	return "This worker gets html and split it to feed.";
    }

	function config()
	{
		return array(
		"handle"=>array("URL","text",""),
		"extract_from"=>array("Cut content from","text",""),
		"extract_to"=>array("to","text",""),
		"delimiter"=>array("Split using delimiter","text","")
		);
	}
    
    function getRegex($page,$pattern)
    {
        if(preg_match('@(?:'.$pattern.')@ims',$page,$match)) {
        	//array_shift($match);
        	return $match[0];//array_values(preg_grep('/.+/',$match));
        }
    }

    function rhacover() {
    	return "1.4.0";
    }
}
?>
