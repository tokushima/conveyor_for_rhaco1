<?php
Rhaco::import("model.PublishBase");
Rhaco::import("io.Snapshot");
/**
 * PublishSnapshot
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PublishSnapshot extends PublishBase{
	function execute($rss){
		$cachename = $this->variable("cachename") or ($cachename = "default");
		switch($this->variable("mode")) {
			case "read":
			$cache = new Snapshot($cachename);
			if($cache->exist($cachename,array(),$this->variable("frequency"))){
				echo $cache->read($cachename);
				echo $cache->get();
				exit;
			}
			break;
			case "writestart":
			Rhaco::setVariable('publishsnapshot__snapshot', new Snapshot($cachename));
			break;
			case "writeend":
			$cache = Rhaco::getVariable('publishsnapshot__snapshot');
			if(!$cache->exist($cachename,array(),$this->variable("frequency"))){
				$cache->set();
				echo $cache->get();
			}
			break;
		}
		return $rss;
	}
	function description(){
		return "Snapshotを作成、表示する。";
	}
	function config(){
		return array(
				"mode"=>array("動作モード","select",
				array("read"=>"表示","writestart"=>"書き込み開始","writeend"=>"書き込み終了")),
				"frequency"=>array("キャッシュ保存時間(s)","text","3600"),
				"cachename"=>"キャッシュ名"
				);
	}
	function rhacover() {
    	return "1.3.0";
    }
}
?>