<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("tag.feed.FeedParser");
Rhaco::import("lang.StringUtil");
Rhaco::import("abbr.V");
/**
 * FeedIn
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class SubscriptionArgs extends SubscriptionBase{
	function execute($rss){
		$urls = array();
		$newrss = new Rss20();
		$requests = $_SERVER['argv'];
		$keys = explode("\n",$this->variable("key"));
		$sets = explode("\n",$this->variable("set"));
		foreach($sets as $key=>$set) {
			if(!preg_match('@^[0-9]+$@',$key)) return $rss;
			if(is_array($requests[$keys[$key]])) {
				$requests[$keys[$key]] = implode("\n",$requests[$keys[$key]]);
			}
			$requests[$keys[$key]] = V::getMagicQuotesOffValue(StringUtil::encode($requests[$keys[$key]]));
			switch ($this->variable("escape")) {
				case "specialchar":
					$requests[$keys[$key]] = htmlspecialchars($requests[$keys[$key]]);
					break;
				case "slash":
					$requests[$keys[$key]] = addslashes($requests[$keys[$key]]);
					break;
				case "regex":
					$requests[$keys[$key]] = preg_quote($requests[$keys[$key]]);
					break;
				case "none":
					break;
				case "entity":
				default:
					$requests[$keys[$key]] = htmlentities($requests[$keys[$key]]);
					break;
			}
			Rhaco::setVariable($set,$requests[$keys[$key]]);
		}
		return $rss;
	}
	function description(){
		return "コマンドライン引数を指定workerのconfigに代入する。配列は要素を改行で置き換えた文字列にまとめられる。連想配列のキーは保存されない。";
	}
	function config(){
		return array(
				"key"=>array("引数番号","textarea"),
				"set"=>array("代入するworker,config名(worker名__config名で指定)","textarea"),
				"escape"=>array("エスケープ法","select",array("entity"=>"htmlエンティティ",
				"specialchar"=>"htmlspecialchars","slash"=>"addslashes",
				"regexp"=>"正規表現用","none"=>"エスケープしない"))
				);
	}
	
	function rhacover() {
		return "1.4.0";
	}
}
?>