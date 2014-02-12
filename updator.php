<?php
/**
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
require_once("__init__.php");
Rhaco::import("Conveyaml");
Rhaco::import("generic.Flow");
Rhaco::import("tag.feed.FeedParser");
Rhaco::import("io.FileUtil");
Rhaco::import("network.Url");
Rhaco::import("network.http.Http");
Rhaco::import("network.http.RequestLogin");
Rhaco::import("exception.model.GenericException");
Rhaco::import("exception.ExceptionTrigger");
Rhaco::import("lang.Variable");
if(Variable::bool(Rhaco::constant("IS_LOGIN"))) RequestLogin::loginRequired();
$server_dl = Url::parseAbsolute(Rhaco::constant("SERVER"),"server/dl.php");
$server_dll = Url::parseAbsolute(Rhaco::constant("SERVER"),"server/dll.php");
$server_wk = Url::parseAbsolute(Rhaco::constant("SERVER"),"server/workers.php");
$server_ln = Url::parseAbsolute(Rhaco::constant("SERVER"),"server/lines.php");
$flow = new Flow();

if($flow->isPost()) {
	if($flow->getVariable("enclosure")) {
		$filer = new FileUtil();
		switch ($flow->getVariable("action")) {
			case "install":
				$pack = Http::post($server_dl,$flow->getVariable("enclosure"));
				if($pack){
					$filer->unpack($pack,Conveyaml::path());
				}else{
					ExceptionTrigger::raise(new GenericException("Http get error."));
				}
				break;
			case "update":
				foreach($flow->getVariable("enclosure") as $dir) {
					$file = FileUtil::path(Rhaco::constant("WORKER_PATH"),str_replace(".","/",$dir).".php");
					if($filer->isFile($file)){
						$enclosure[] = "/".str_replace(".","/",$dir).".php";
					}else{
						ExceptionTrigger::raise(new NotFoundException($dir));
					}
				}
				$filer->unpack(Http::post($server_dl,$enclosure),Conveyaml::path());
				break;
			case "remove":
				foreach($flow->getVariable("enclosure") as $dir) {
					$file = FileUtil::path(Rhaco::constant("WORKER_PATH"),str_replace(".","/",$dir).".php");
					if($filer->isFile($file)){
						$filer->rm(dirname($file));
					}else{
						ExceptionTrigger::raise(new NotFoundException($dir));
					}
				}
				break;
			case "installlines":
				$pack = Http::post($server_dll,array("target"=>$flow->getVariable("enclosure"),"type"=>"line"));//仮

				if($pack){
					$filer->unpack($pack,Rhaco::constant('PUBLISH_DIR',Rhaco::path('publish')));
				}else{
					ExceptionTrigger::raise(new GenericException("Http get error."));
				}
				break;
			default:
				break;
		}
	}
}else{
	switch ($flow->getVariable("action")) {
		case "install":
			$rss = FeedParser::read($server_wk);
			foreach($rss->getItem() as $obj){
				$list[] = array("name"=>$obj->getTitle(),"description"=>TemplateFormatter::getCdata(TemplateFormatter::htmldecode($obj->getDescription())),"type"=>$obj->getCategory(),"time"=>$obj->getPubDate(),"value"=>$obj->getLink());
			}
			$json = array("modules"=>$list);
			header("Content-type: text/json; charset=utf-8");
			echo Variable::toJson($json);
			break;
		case "update":
		case "remove":
			break;
		case "installlines":
			$rss = FeedParser::read($server_ln);
			foreach($rss->getItem() as $obj){
				$list[] = array("name"=>$obj->getTitle(),"description"=>TemplateFormatter::getCdata(TemplateFormatter::htmldecode($obj->getDescription())),"time"=>$obj->getPubDate(),"value"=>$obj->getLink());
			}
			$json = array("modules"=>$list);
			header("Content-type: text/json; charset=utf-8");
			echo Variable::toJson($json);
			break;
		case "updatelines":
		case "removelines":
			break;
			default:
			break;
	}
}
?>