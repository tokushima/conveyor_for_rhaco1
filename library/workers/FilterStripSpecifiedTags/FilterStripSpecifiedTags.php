<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("tag.model.TemplateFormatter");
Rhaco::import("tag.model.SimpleTag");
/**
 * FilterStripSpecifiedTags
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterStripSpecifiedTags extends FilterBase{
    
    function execute($rss20)
    {
    	if(!$this->variable("mode")) return $rss20;
        $channel = $rss20->getChannel();
        $items = $rss20->getItem();
        $properies = explode("\n",$this->variable("property"));
        $remove_tags = explode("\n",$this->variable("remove"));
        
        $rss20_filtered = new Rss20();
        $rss20_filtered->setChannel($channel->getTitle(),
            $channel->getDescription(),
            $channel->getLink(),
            "ja"
        );
        if(empty($properies)) {
        	return $rss20;
        }
        foreach ($items as $key=>$item) {
        	foreach($properies as $property) {
        		$getter = "get".ucfirst($property);
        		$setter = "set".ucfirst($property);
        		$source = TemplateFormatter::getCdata(call_user_func(array($item,$getter)));
       			$source = "<body>$source</body>";
        		do{
         			SimpleTag::setof($tag,$source,"body");
        			$tags_exist = false;
	        		foreach($remove_tags as $remove_tag){
	        			foreach($tag->getIn($remove_tag) as $rtag){
	        				if($this->variable("mode")==="content"){
	        					$source = str_replace($rtag->getPlain(),'',$source);
	        				}elseif($this->variable("mode")==="tag"){
	        					$source = str_replace($rtag->getPlain(),$rtag->getValue(),$source);
	        				}
	        				$tags_exist = true;
	        			}
	        		}
        		}while($tags_exist);
        		$source = str_replace(array("<body>","</body>"),"",$source);
        		call_user_func(array(&$item,$setter),$source);
        	}
            $rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "指定タグを削除する";
    }

	function config()
	{
		return array(
			"mode"=>array("削除動作","select",array("tag"=>"タグのみ削除","content"=>"タグの内容を含めて削除")),
			"property"=>array("プロパティ","textarea","title"),
			"remove"=>array("削除するタグ","textarea","script\nnoscript\nimg\niframe\nform\nobject")
		);
	}
	
	function rhacover(){
		return "1.2.0";
	}
}
?>
