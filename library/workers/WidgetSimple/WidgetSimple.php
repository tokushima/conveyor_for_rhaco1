<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("abbr.V");
/**
 * WidgetSimple
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class WidgetSimple extends PublishBase{
    
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
        	if(!method_exists($item,"getWidget")){
        			$item = V::mixin($item,V::anonym("widget",array("setWidget",'$name,$value','$this->widget[$name]=$value;'),array("getWidget",'$name=""','if(empty($name))return $this->widget; else return $this->widget[$name];')));
        	}
        	$simple['url'] = $this->variable("url");
        	$content = $this->variable("content");
        	if(strstr($content,"$")) {
        		$simple['content'] = eval($content);
        	}
        	$item->setWidget("simple",$simple);
        	$rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "Widgetを作成する";
    }

	function config()
	{
		return array(
			"yaml"=>array("Widget設定のyamlファイル","textarea"),
			"link"=>"リンク",
			"query"=>"コマンド",
			"content"=>"Widgetの内容"
		);
	}
	
	function rhacover(){
		return "1.3.1";
	}
}
?>
