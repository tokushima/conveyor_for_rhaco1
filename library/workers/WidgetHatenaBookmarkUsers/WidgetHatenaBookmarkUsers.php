<?php
Rhaco::import("model.PublishBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("abbr.V");
/**
 * WidgetHatenaBookmarkUsers
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class WidgetHatenaBookmarkUsers extends PublishBase{
    
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
        	$hatena = array("url"=>"http://b.hatena.ne.jp/entry/".$item->getLink());
        	if(method_exists($item,"getMeta") && $item->getMeta("hatenabookmark_users")) {
        		$user = $item->getMeta("hatenabookmark_users") or ($user = 0);
        		if($user < 2) { 
        			$hatena["content"] = $user;
        		}elseif($user >= 2 && $user < 10) {
        			$hatena["content"] = "<em style=\"background-color: #fff0f0; font-weight: bold; display: inline; font-style: normal;\"><span style=\"color: #ff6666;\">".$user."</span></em>";
        		}elseif($user >= 10) {
        			$hatena["content"] = "<strong style=\"background-color: #ffcccc; font-weight: bold; font-style: normal; display: inline;\"><span style=\"color: red;\">".$user."</span></strong>";
        		}
        		$item->setWidget("hatena",$hatena);
        	}else{
        		$size = ($this->variable("size"))? $this->variable("size") : "small";
        		$hatena["content"] = "<img src=\"http://b.hatena.ne.jp/entry/image/".$size."/".$item->getLink()."\" style=\"border:0;vertical-align:middle\" />";
        		$item->setWidget("hatena",$hatena);
        	}
        	$rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "はてなブックマークに登録した人の数を表示するボタンを作成する";
    }

	function config()
	{
		return array(
			"size"=>array("サイズ","select",array("small"=>"小","middle"=>"中","large"=>"大"))
		);
	}

	function rhacover(){
		return "1.3.1";
	}
	
}
?>
