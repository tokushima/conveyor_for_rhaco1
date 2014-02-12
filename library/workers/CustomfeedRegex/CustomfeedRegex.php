<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("lang.StringUtil");
Rhaco::import("network.http.Browser");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("tag.feed.Rss20");
/**
 * CustomFeedKanpo
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class CustomfeedRegex extends SubscriptionBase{
    function execute($variable){
		$browser = new Browser();
		$tag = new SimpleTag();
		$url = $this->variable("url");
		$url = preg_replace('@([^/])$@','$1/',$url);
    	$page = StringUtil::encode($browser->get($url));
    	$tag->set($page);
        $title = $tag->getInValue("title");
        $rss20 = new Rss20();
        $rss20->setChannel($title,
            "description",
            $url,
            "ja"
        );
        
        $item = new RssItem20();
        $item->setTitle($title);
        $item->setLink($url);
        $item->setPubDate(time());
        if(preg_match('@'.$this->variable("extract").'@ms',$tag->getPlain(),$match)) {
        	array_shift($match);
        	$description = preg_replace('@(href\s*?=\s*?["\'])(?!http)@','$1'.$url,implode("",$match));
        	$description = str_replace($url."/",$url,$description);
        	$item->setDescription($description);
        }
        $item->setCategory("not list");
		$rss20->setItem($item);
		$rss20 = $this->merge($variable,$rss20);
        return $rss20;
    }
    function description(){
    	return "ページの正規表現で選択した箇所を取得する";
    }
    function config(){
        $config = array(
        	"url"=>"取得URL",
            "extract" => "取得用正規表現",
        );

        return $config;
    }
}

?>
