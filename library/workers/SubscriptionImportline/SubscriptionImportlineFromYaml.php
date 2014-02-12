<?php
Rhaco::import("model.SubscriptionBase");

/**
 *
 * SubscriptionImportline
 *
 * @author SHIGETA Takeshiro
 */
class SubscriptionImportlineFromYaml extends SubscriptionBase{
	function execute($variable){
		$yaml = Spyc::YAMLLoad(dirname(__FILE__)."/".$this->variable("file").".yaml");
		$stock = Rhaco::getVariable("conveyaml__yaml");
		Rhaco::setVariable("conveyaml__yaml",$yaml['plugins']);
		if($this->variable("argument")) {
			array_unshift($yaml['plugins'],array('module'=>'Subscription.SubscriptionRssObject','config'=>array('rss'=>$variable)));
		}
		$result = Conveyaml::execute();
		Rhaco::setVariable("conveyaml__yaml",$stock);
		return $this->merge($result,$variable);
	}
	function description(){
		return "他のユーザースクリプトを実行する";
	}
	function config(){
		return array(
				"file"=>array("使用するユーザースクリプト","text"),
				"argument"=>array("ユーザースクリプトに現在までのフィードを渡しますか？","select",array("1"=>"はい","0"=>"いいえ"))
				);
	}
	function required(){
		return array("workers.Subscription.SubscriptionRssObject"=>"");
	}
	function rhacover() {
    	return "1.2.0";
    }
}
?>