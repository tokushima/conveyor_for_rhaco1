<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("io.FileUtil");
/**
 *
 * SubscriptionText
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class SubscriptionText extends SubscriptionBase{
    
    function execute($rss)
    {
    	$filer = new FileUtil();
    	$channel = $rss->getChannel();
        $items = $rss->getItem();
        $source = $filer->read(FileUtil::path(Rhaco::constant("PUBLISH_PATH"),$this->variable("filename")));
        $splitter = str_replace(array("tab","camma"),array("\t",","),$this->variable("splitter"));
        foreach (explode("\n",$source) as $item) {
        	$rssitem = new RssItem20();
        	$values = array_map('trim',explode($splitter,$item));
        	foreach(explode("\n",$this->variable("properties")) as $key=>$property) {
        		$method_name = "set".ucfirst($property);
        		call_user_func(array(&$rssitem,$method_name),$values[$key]);
        	}
        	$rss->setItem($rssitem);
        }
        return $rss;
    }
        
    function description() {
    	return "Textファイル(CSVファイルなど)から読み込む";
    }

	function config()
	{
		return array(
		"filename"=>array("ファイル名","text","rss",true),
		"properties"=>array("出力するプロパティ名","textarea","",true),
		"splitter"=>array("区切り文字(デフォルトはtab)","select",array("tab"=>"タブ","camma"=>"カンマ"))
		);
	}

	function rhacover() {
    	return "1.3.0";
    }
}
?>
