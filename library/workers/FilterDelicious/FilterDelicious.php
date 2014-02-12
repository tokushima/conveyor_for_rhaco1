<?php
Rhaco::import("model.FilterBase");
Rhaco::import("network.http.Http");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("Jsphon");
Rhaco::import("abbr.V");
/**
 * FilterDelicious
 * 
 * Created on 2007/02/13
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 
 * @author SHIGETA Takeshiro 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */

class FilterDelicious extends FilterBase{
    
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
        	$json_total_parameters[] = md5($item->link);
        }
        $json_results = array();
        $json_parameters = array_chunk($json_total_parameters,15);
        foreach($json_parameters as $params) {
        $query = "http://badges.del.icio.us/feeds/json/url/data?hash=".implode("&hash=",$params);
    	$query_results = Jsphon::decode(Http::body($query));
    	$json_results = array_merge($json_results,$query_results);
        }
		$result = array();
		foreach($json_results as $json_result) {
			$results[$json_result['url']] = $json_result;
		}
		foreach($items as $key=>$item) {
      		if(!method_exists($item,"getMeta")){
       			$item = V::mixin($item,V::anonym("meta",array("getMeta",'$name','if(isset($this->meta[$name])) return $this->meta[$name];'),array("setMeta",'$name,$value','$this->meta[$name]=$value;')));
       		}
			$item->setMeta("delicious_users",$results[$item->getLink()]['total_posts']);
			if(is_array($results[$item->getLink()]['top_tags'])) {
			$tag = ($item->getMeta("tag"))? $item->getMeta("tag") : array();
			$cloud = ($item->getMeta("cloud"))? $item->getMeta("cloud") : array();
			$tag = $tag + array_keys($results[$item->getLink()]['top_tags']);
			$cloud = $cloud + $results[$item->getLink()]['top_tags'];
			$item->setMeta("tag",$tag);
			$item->setMeta("cloud",$cloud);
			}
			$rss20_filtered->setItem($item);
		}
        return $rss20_filtered;
    }
    
    function description() {
    	return "Del.icio.usのブックマーク数をカウントする";
    }

	function required () {
		return array(
			"Jsphon"=>"http://www.hawklab.jp/jsonencoder/"
		);
	}	

	function rhacover(){
		return "1.3.1";
	}
}
?>
