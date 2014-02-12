<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("arbo.network.Xmlrpc");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("tag.model.TemplateFormatter");
/**
 *
 * FilterHatenaDiaryKeywordLink
 * @author SHIGETA Takeshiro
 * 


 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */

class FilterHatenaDiaryKeywordLink extends FilterBase{
    
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
        $rpc = new XmlrpcClient("http://d.hatena.ne.jp/xmlrpc");
        $tag = new SimpleTag();
        $rpc->setMethod("hatena.setKeywordLink");
        foreach($items as $item) {
        	$descriptions[] = TemplateFormatter::getCdata($item->getDescription());
		}
		$body = implode("##".get_class($this)."##",$descriptions);
    	$obj = new stdClass();
    	$obj->body = $body;
    	$obj->score = $this->variable("score") or ($obj->score = 20);
    	$obj->a_target = "_blank";
    	$obj->a_class = "keyword";
    	$rpc->push($obj);
    	$tag->set($rpc->request());
    	$results = explode("##".get_class($this)."##",$tag->getInValue("string"));
        foreach($items as $key=>$item) {
        	$item->setDescription($results[$key]);
			$rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "はてなキーワードを抽出する";
    }
    
    function config () {
		return array("score"=>"スコア");
	}
	
	function required () {
		return array(
			"Xmlrpc"=>"arbo"
		);
	}	

	function rhacover(){
		return "1.2.0";
	}
}
?>
