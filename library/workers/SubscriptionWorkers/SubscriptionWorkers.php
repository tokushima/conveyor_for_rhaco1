<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("io.FileUtil");
/**
 * SubscriptionWorkers
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class SubscriptionWorkers extends SubscriptionBase{
	function execute($variable){
		$urls = array();
		$newrss = new Rss20();
		$newrss->setChannel("Filelist");
		$filer = new FileUtil();
		$path = $this->variable("path");
		$dirs = FileUtil::dirs($path);
		
		foreach($dirs as $dir){
			str_replace('//','/',$dir);
			$filename = FileUtil::path($dir,basename($dir).$this->variable("extension"));
			if(FileUtil::exist($filename)){
				$file = new File($filename);
				if($file->getOriginalName() !== "SubscriptionWorkers" && !preg_match('@^message-@',$file->getOriginalName())) {
					$item = new RssItem20();
					$item->setTitle($file->getOriginalName());
					$item->setPubDate($file->getUpdate());
					$item->setLink($file->getFullName());

					$obj = Rhaco::obj($file->getFullName());

					switch (true) {
						case Variable::istype("SubscriptionBase",$obj):
							$item->setCategory("subscription");
							break;
						case Variable::istype("FilterBase",$obj):
							$item->setCategory("filter");
							break;
						case Variable::istype("PublishBase",$obj):
							$item->setCategory("publish");
							break;
						default:
							break;
					}
					$item->setDescription($obj->getDescription());
					$comments = array();
					if($obj->required()) {
						$comments[] = "require ".implode(", ",array_keys($obj->required()));
					}
					if($obj->rhacover()){
						$comments[] = "rhaco version ".trim($obj->rhacover());
					}
					$item->setComments(implode("\n",$comments));
					$newrss->setItem($item);
				}
			}
		}
		return $this->merge($variable,$newrss);
	}
	function description(){
		return "Workerリストを取得する";
	}
	function config(){
		return array(
				"path"=>array("ファイルリストを取得するディレクトリ","text",Rhaco::lib(),true),
				"recursive"=>array("ファイルリストを再帰的に取得するか?","select",array(1=>"する",0=>"しない")),
				"extension"=>array("拡張子")
				);
	}
	function rhacover(){
		return "1.5.0";
	}
}
?>