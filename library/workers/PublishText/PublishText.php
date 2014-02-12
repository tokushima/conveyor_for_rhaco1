<?php
Rhaco::import("model.PublishBase");
Rhaco::import("io.FileUtil");
/**
 *
 * PublishText
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PublishText extends PublishBase{
    
    function execute($rss)
    {
    	$filer = new FileUtil();
    	$channel = $rss->getChannel();
        $items = $rss->getItem();
        $source = "";
        $splitter = str_replace(array("tab","camma"),array("\t",","),$this->variable("splitter"));
        foreach ($items as $item) {
        	$parts = array();
        	foreach(explode("\n",$this->variable("properties")) as $property) {
        		$method_name = "get".ucfirst($property);
        		if(method_exists($item,$method_name)){
        			$parts[] = str_replace(array("\r","\n"),"",$item->$property);
        		}
        	}
        	$source = $source.implode($splitter,$parts)."\n";
        }
        if($this->variable("append")) {
	        $filer->append(FileUtil::path(Rhaco::constant("PUBLISH_PATH"),$this->variable("filename").".txt"),$source,$this->variable("encode"));
        }else{
	        $filer->write(FileUtil::path(Rhaco::constant("PUBLISH_PATH"),$this->variable("filename").".txt"),$source,$this->variable("encode"));
        }
        return $rss;
    }
        
    function description() {
    	return "Textファイルに出力する";
    }

	function config()
	{
		return array(
		"filename"=>array("ファイル名","text","rss",true),
		"properties"=>array("出力するプロパティ名","textarea","",true),
		"splitter"=>array("区切り文字(デフォルトはtab)","select",array("tab"=>"タブ","camma"=>"カンマ")),
		"encode"=>array("エンコード","select",array("UTF-8"=>"UTF-8","EUC-JP"=>"EUC-JP","SJIS"=>"Shift JIS","JIS"=>"JIS")),
		"append"=>array("書き込み方法","select",array(0=>"上書き",1=>"追記"))
		);
	}

	function rhacover() {
    	return "1.3.0";
    }
}
?>
