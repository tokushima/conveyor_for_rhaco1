<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("network.http.Http");
Rhaco::import("lang.StringUtil");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("io.FileUtil");
Rhaco::import("abbr.V");
Rhaco::import("arbo.tag.Xmlpath");
Rhaco::import("arbo.lang.ExtractDate");
/**
 * FilterEntryFullText
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 * @version 0.1.0
 * 0.1.0 add getpage option
 */
class FilterEntryFullText extends FilterBase{
    
	function FilterEntryFullText(){
		$this->__init__(__FILE__);
	}
	
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
        $fp = new FileUtil();
        $files = $fp->find('@\.yaml$@',dirname(__FILE__));
        $getpage = $this->variable("get_page") or ($getpage = 1);
        foreach($files as $file) {
			$configs[] = Spyc::YAMLLoad($file->fullname);
        }
        foreach ($items as $item) {
        	$page = "";
        	foreach($configs as $config) {
	        	if($config["handle"]) {
	        		$applicable = preg_match('@(?:'.$config["handle"].')@i',$item->link);
			    	if($applicable && empty($page)) {
			    		$link = $item->getLink();
			    		if($link[0] == '/'){
			    			$filer = new FileUtil();
			    			$dpage = $filer->read($link);
			    		}else{
			    			$dpage = Http::body($link);//$browser->get($link);
			    		}//var_dump($link);var_dump($dpage);
			    		if(preg_match('@content=.*?charset=([a-zA-Z0-9\-\_]+).*?>@i',$dpage,$charset)) {
			    			$page = mb_convert_encoding($dpage,"UTF-8",$charset[1]);
			    		}else{
				    		$page = StringUtil::encode($dpage);
			    		}
			    	}
	        	}elseif($config["match"]) {
	        		if(!$getpage) $page = $item->getDescription();
			    	if(empty($page)) $page = StringUtil::encode(Http::body($item->link));
			    	$applicable = preg_match('@(?:'.$config["match"].')@i',$page);
	        	}elseif($config["source_handle"]){
	        		$source = $item->getSource();
	        		if(V::istype("RssSource",$source)) {
		        		$applicable = preg_match('@(?:'.$config["source_handle"].')@i',$source->getUrl());
				    	if($applicable && empty($page)) {
	        				if($getpage){
				    			$dpage = Http::body($item->link);
	        				}else{
	        					$dpage = $item->getDescription();
	        				}
				    		if(preg_match('@content=.*?charset=([a-zA-Z0-9\-\_]+).*?>@i',$dpage,$charset)) {
				    			$page = mb_convert_encoding($dpage,"UTF-8",$charset[1]);
				    		}else{
					    		$page = StringUtil::encode($dpage);
				    		}
				    	}
	        		}
	        	}else{
	        		$applicable = false;
	        	}
	        	if($applicable){//var_dump($config);
	        		$applicable = false;
	        		$match = array();
	        		if($config["extract"]) {
	        			$match = $this->getRegex($page,$config["extract"]);
	        		}elseif($config["extract_xpath"]) {
	        			$page = preg_replace('@<!--.*?-->@ims','',$page);
	        			foreach(explode("\n",$config["extract_xpath"]) as $xpath){
	        				$match = array_merge($match,$this->getIn($page,$xpath));
	        			}//var_dump($match);
	        		}
		            if(!empty($match)) {
		            	$newmethod = false;
		            	$captures = explode("\n",$config["extract_capture"]);
		            	foreach($captures as $key=>$var) {
		            			$setter = "set".ucfirst($var);
		            			$getter = "get".ucfirst($var);
		            			if(!method_exists($item,$setter)) {
		            				$newmethod = true;
       								$meds[] = 'array(\'set'.ucfirst($var).'\',\'$value\',\'$this->'.$var.' = $value;\'),array(\'get'.ucfirst($var).'\',\'\',\'return $this->'.$var.';\')';
		            				$props[] = '"'.$var.'"';
		            			}
		            	}
		            	if($newmethod){
		            		eval('$item = V::mixin($item,V::anonym('.implode(',',$props).','.implode(',',$meds).'));');
		            	}
		            	
		            	foreach($captures as $key=>$var) {
		            		if(!empty($match[$key])){
		            			$setter = "set".ucfirst($var);
		            			$getter = "get".ucfirst($var);
		            			if(method_exists($item,$setter)) {
		            				call_user_func(array(&$item,$setter),StringUtil::encode($match[$key]));
		            			}
		            		}
		            	}
		    			if($config["extract_date_format"] && class_exists("ExtractDate")) {
		    				$time = ExtractDate::parse($page,$config["extract_date_format"]);
		    				$item->setPubDate($time);
		    			}
		    			if($config["author"]) {
		    				$item->setAuthor($config["author"]);
		    			}
		            }elseif($this->variable("store_html_on_failure")) {
		            	$tag = new SimpleTag();
		            	$tag->set($page);
		            	$item->setDescription($tag->getInValue("body"));
		            }
		            if($config["extract_after_hook"]) {//var_dump($config["extract_after_hook"]);
		            	eval($config["extract_after_hook"]);
		            }
		            break;
	        	}
        	}
           	$rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "This worker gets full text of article.";
    }

	function config()
	{
		return array(
		"get_page"=>array("Get each page?","select",array("1"=>"Yes","0"=>"No")),
		"store_html_on_failure"=>array("Store HTML if search failed?","select",array("1"=>"Yes","0"=>"No"))
		);
	}
    
    function getRegex($page,$pattern)
    {
        if(preg_match('@(?:'.$pattern.')@ims',$page,$match)) {
        	array_shift($match);
        	return array_values(preg_grep('/.+/',$match));
        }
    }
    
    function getIn ($page,$pattern) {
    	$match = array();
		$tag = new SimpleTag();
		$tag->set($page);
		$getinlist = explode('\n',$pattern);
		foreach($getinlist as $key=>$getin) {
			$result = Xmlpath::parse($getin,$tag);
			if(is_array($result)) {
				if(strtolower(get_class($result[0])) == "simpletag") {
				$match[$key] = $result[0]->getValue();					
				}			
			}
		}
		return $match;
	}

	function required () {
		return array(
			"Xmlpath"=>"arbo",
			"ExtractDate"=>"arbo"
		);
	}

    function rhacover() {
    	return "1.2.0";
    }

	function stripLinkList($script){
		$linklist = array('div','p','ul','dl','ol');
		$tag = new SimpleTag();
		$tag->set("<body>$script</body>");
		foreach($linklist as $list){
			foreach($tag->getIn($list) as $listtag){
				$lines = StringUtil::strlen(strip_tags($listtag->getValue()));
				$as = 0;
				foreach($listtag->getIn('a') as $a_tag){
					$as = $as + StringUtil::strlen($a_tag->getValue());
				}//var_dump($as/$lines);var_dump($listtag->getValue());
				if($as/$lines > 0.8)
					$script = str_replace($listtag->getPlain(),'',$script);
			}
		}
		return $script;
	}
}
?>
