<?php
Rhaco::import("model.PublishBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("abbr.V");
/**
 * WidgetDelicious
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class WidgetDelicious extends PublishBase{
    
    function execute($rss20)
    {
    	if(!class_exists("FilterDelicious")) return $rss20;
        $channel = $rss20->getChannel();
        $items = $rss20->getItem();

        $rss20_filtered = new Rss20();
        $rss20_filtered->setChannel($channel->getTitle(),
            $channel->getDescription(),
            $channel->getLink(),
            "ja"
        );
        $properties = explode("\n",$this->variable("property"));
        foreach($items as $item) {
        	if(!method_exists($item,"getMeta")) {
        		return $rss20;
        	}
        	if(!method_exists($item,"getWidget")){
        			$item = V::mixin($item,V::anonym("widget",array("setWidget",'$name,$value','$this->widget[$name]=$value;'),array("getWidget",'$name=""','if(empty($name))return $this->widget; else return $this->widget[$name];')));
        	}
        	$delicious = array();
        	$users = $item->getMeta("delicious_users") or ($users = false);
       		$delicious["url"] = "http://badges.del.icio.us/url/".md5($item->getLink());
        	$content = "";
        	if($users && in_array("users",$properties)) {
    			$content = $content."<div class=\"users\">";
        		if($user < 2) { 
        			$content = $user;
        		}elseif($users >= 2 && $users < 10) {
        			$content = "<em style=\"background-color: #fff0f0; font-weight: bold; display: inline; font-style: normal;\"><span style=\"color: #ff6666;\">".$user."</span></em>";
        		}elseif($users >= 10) {
        			$content = "<strong style=\"background-color: #ffcccc; font-weight: bold; font-style: normal; display: inline;\"><span style=\"color: red;\">".$user."</span></strong>";
        		}
        		$content = $content."</div>";
        	}
        	if($item->getMeta("tag") && in_array("tag",$properties)) {
        		$content = $content."<div class=\"tag\">".implode(" ",$item->getMeta("tag"))."</div>";
        	}
        	if($item->getMeta("cloud") && in_array("cloud",$properties)) {
        		$content = $content."<div class=\"cloud\">".implode(" ",$item->getMeta("cloud"))."</div>";
        	}
        	$delicious["content"] = $content;
        	$item->setWidget("delicious",$delicious);
        	$rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "Deliciousに登録した人の数やタグなどを表示するボタンを作成する";
    }

	function config()
	{
		return array(
			"property"=>array("表示する内容(users,tag,cloud)","textarea","users\ntag\ncloud",true),
		);
	}
	
	function rhacover(){
		return "1.3.1";
	}
	
}
?>
