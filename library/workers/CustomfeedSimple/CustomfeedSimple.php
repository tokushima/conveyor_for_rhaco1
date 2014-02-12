<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.StringUtil");
Rhaco::import("network.http.Browser");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("arbo.tag.Xmlpath");
Rhaco::import("arbo.lang.ExtractDate");
Rhaco::import("abbr.V");
/**
 * CustomFeedSimple
 * @author SHIGETA Takeshiro
 *


 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class CustomfeedSimple extends SubscriptionBase{

	var $configs = array();
	var $browser;

	function CustomfeedSimple(){
		$this->__init__(__FILE__);
	}

    function execute($rss){
    	//set config
    	$fp = new FileUtil();
		$this->browser = new Browser();
		$pubdate = array();
        $files = $fp->find('@\.yaml$@i', dirname(__FILE__));
        foreach($files as $file) {
					$this->configs[] = Spyc::YAMLLoad($file->fullname);
        }
        //convert items to custom feed
    	$channel = $rss->getChannel();
        $items = $rss->getItem();
    	$rss20 = new Rss20();
        $rss20->setChannel(
			'Customfeeds',
            "",
            "",
            "ja"
        );
    	foreach($items as $item){
    		$newitems = $this->_getCustomFeed($item);
    		if(empty($newitems)) $rss20->setItem($item);
    		foreach($newitems as $newitem){
    			$rss20->setItem($newitem);
    		}
    	}
    	//get customfeed from setting
        foreach(explode("\n",$this->variable("url")) as $url) {
        	$newitems = $this->_getCustomFeed($url);
        	foreach ($newitems as $newitem){
        		$rss20->setItem($newitem);
        	}
        }
        return $rss20;
    }

    function _getCustomFeed($url){
    	if(V::istype('RssItem',$url)) $url = $url->getLink();
    	if(empty($url)) return false;
    	$returnitem = array();
   		$page = "";
       	$time = array();
       	foreach($this->configs as $config) {
       		if($config["handle"]=="*" || $config["handle"]==$url || preg_match('@'.$config["handle"].'@',$url)) {
	   			if(empty($page)) $page = StringUtil::encode($this->browser->get($url));
				$tag = new SimpleTag();
				$tag->set($page);
				//set sourcetag
				$title = $tag->getInValue("title");
		        $source = new RssSource($url,$title);
		        //narrower down
		        if(array_key_exists("narrow_regex",$config)) {
			       	$extractpage = "<div>".$this->getRegex($page,$config["narrow_regex"])."</div>";
		        	$tag->set($extractpage);
		        }elseif(array_key_exists("narrow_xpath",$config)) {
		        	$extractpage = $this->getIn($page,$config["narrow_xpath"]);
		        	$tag->set($extractpage);
		        }
	        	//get date
	    		if(array_key_exists("extract_date_format",$config) && class_exists("ExtractDate")) {
	    			$regex = ExtractDate::getPattern($config["extract_date_format"]);
	    			if(preg_match_all('@'.$regex.'@',$tag->plain,$match)) {
	    				foreach($match[0] as $matchedstring) {
	    					$time[] = ExtractDate::parse($matchedstring,$config["extract_date_format"]);
	    				}
	    			}
	    		}
	       		$key=0;
				foreach($tag->getIn("a") as $a_tag){
					if(preg_match('@'.$config["follow_link"].'@im', $a_tag->getParameter("href"))) {
		    				$title = $a_tag->getValue();
		    				$href = $a_tag->getParameter("href");
		    				if($href[0]=='.'){
		    					$link = Url::parseAbsolute($url,$href);
		    				}elseif(preg_match('@^(?:http|https)://@',$href)){
		    					$link = $href;
		    				}else{
		    					if(preg_match('@(^http://[^/]+)@',$url,$match)){
		    						$link = Url::parseAbsolute($match[1],$href);
		    					}
		    				}
		    				$link = str_replace(' ','%20',$link);
		    				$item = new RssItem20();
			    			$item->setTitle($title);
			    			$item->setLink($link);
			    			$pubdate = (empty($time[$key]))? time() : $time[$key];
			    			$item->setPubDate($pubdate);
			    			$item->setSource($source);
				            if(array_key_exists("extract_after_hook",$config)) {//var_dump($config["extract_after_hook"]);
				            	eval($config["extract_after_hook"]);
				            }
			    			$returnitem[] = $item;
			    			$key++;
						}
					}
        		}
        	}
        	return $returnitem;
	}

    function description(){
    	return "This worker extracts links from HTML.";
    }

	function config () {
		return array(
			"url"=>array("URL list","textarea")
		);
	}

    function getRegex($page,$pattern)
    {
        if(preg_match('@(?:'.$pattern.')@ims',$page,$match)) {
        	array_shift($match);
        	return implode('',$match);
        }
    }

    function getIn ($page,$pattern) {
    	$match = "";
		$tag = new SimpleTag();
		$tag->set($page);
		$getinlist = explode(';',$pattern);
		foreach($getinlist as $key=>$getin) {
			$result = Xmlpath::parse($getin,$tag);
			if(is_array($result)) {
				if(strtolower(get_class($result[0])) == "simpletag") {
				$match .= $result[0]->getPlain();
				}			}
		}
		return $match;
	}

	function required () {
		return array(
			"arbo.tag.Xmlpath"=>"arbo.tag",
			"arbo.lang.ExtractDate"=>"arbo.lang"
		);
	}

    function rhacover() {
    	return "1.2.0";
    }

}

?>
