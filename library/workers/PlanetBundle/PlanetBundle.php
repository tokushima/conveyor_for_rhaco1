<?php
Rhaco::import("model.PublishBase");
Rhaco::import("io.FileUtil");
/**
 * Bundle
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PlanetBundle extends PublishBase{
	function execute($variable){
		$stock = Rhaco::getVariable("conveyaml__yaml");
		$scriptname = $_SERVER["SCRIPT_NAME"];
		$linename = basename($_SERVER["SCRIPT_FILENAME"],".php");
		$yaml_set[] = array("module"=>"PlanetSubscriptionSetRss.PlanetSubscriptionSetRss","config"=>array("rss"=>$variable));
		$yaml_set[] = array("module"=>"PlanetPublishFeed.PlanetPublishFeed",
		"config"=>array("title"=>$this->variable("title"),
		"description"=>$this->variable("description"),
		"dir"=>FileUtil::path(Rhaco::constant("PUBLISH_PATH"),$linename."/rss20.xml"), //rssを書き出すディレクトリはpublish/(line name)/rss20.xmlとなる
		"url"=>Rhaco::url($scriptname)));
		$yaml_set[] = array("module"=>"PlanetPublishHtml.PlanetPublishHtml",
		"config"=>array("title"=>$this->variable("title"),
		"description"=>$this->variable("description"),
		"feedpath"=>FileUtil::path(str_replace(Rhaco::constant("CONTEXT_PATH"),"",Rhaco::constant("PUBLISH_PATH")),$linename."/rss20.xml"),
		"stylesheet"=>$this->variable("stylesheet")));
		Rhaco::setVariable("conveyaml__yaml",$yaml_set);
		$result = Conveyaml::execute();
		Rhaco::setVariable("conveyaml__yaml",$stock);
		return $result;
	}
	function description(){
		return "Planetサイトを作成する";
	}
	function config(){
		$list = array();
		$io = new FileUtil();
		foreach($io->find('/\.css$/',Conveyaml::path("PlanetPublishHtml/templates/style")) as $file){
			$list[$file->getOriginalName()] = $file->getOriginalName();
		}
		return array(
				"title"=>"サイトのタイトル",
				"description"=>array("サイトの説明","textarea"),
				"stylesheet"=>array("使用するスタイルシート","select",$list),
				);
	}
	function required () {
		return array(
			"PlanetSubscriptionSetRss.PlanetSubscriptionSetRss"=>"",
			"PlanetPublishFeed.PlanetPublishFeed"=>"",
			"PlanetPublishHtml.PlanetPublishHtml"=>""
		);
	}
	function rhacover() {
    	return "1.3.0";
    }
}
?>