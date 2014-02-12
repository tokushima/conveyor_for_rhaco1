<?php
Rhaco::import("util.Automator");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("io.FileUtil");
Rhaco::import("model.SubscriptionBase");
Rhaco::import("model.FilterBase");
Rhaco::import("model.PublishBase");
Rhaco::import("spyc");
/**
 * @author kazutaka tokushima
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class Conveyaml{
	/**
	 * load worker modules
	 * @param void
	 * @return unknown
	 */
	function loadModule(){
		/**
		 * $loadedWorkers = Conveyaml::loadModule();
		 * $dirs = FileUtil::dirs(Conveyaml::path());
		 * $phps = FileUtil::find('@\.php$@',Conveyaml::path());
		 * //数を比較
		 * if(!empty($dirs)){
		 * foreach($dirs as $dir){
		 *   //basename($dir)がクラス名にあるかチェック
		 *   //subscriptionが付いてればsubscriptionにあるかチェック
		 *   //以下・・・
		 * }
		 * }
		 * if(!empty($phps)){
		 * foreach($phps as $file){
		 *   //$file->getName()がクラス名にあるかチェック
		 * }
		 * }
		 */
		$variable　= array("subScription"=>array(),"filter"=>array(),"publish"=>array());
		$dirs = FileUtil::dirs(Conveyaml::path());
		sort($dirs);

		foreach($dirs as $dir){
			$filename = FileUtil::path($dir,basename($dir).'.php');
			if(FileUtil::exist($filename)){
				$object = Rhaco::obj($filename);

				if(Variable::istype("WorkerBase",$object)){
					$object->setRealpath($filename);
					$label = $object->label();

					if(Variable::istype("SubscriptionBase",$object)){
						$variable["subscription"][$label] = $object;
					}else if(Variable::istype("FilterBase",$object)){
						$variable["filter"][$label] = $object;
					}else if(Variable::istype("PublishBase",$object)){
						$variable["publish"][$label] = $object;
					}
					$variable["module"][$label] = $object;
					$variable["classname"][$label] = strtolower(get_class($object));
				}
			}
		}
		if(!empty($variable)) ksort($variable);
		return $variable;
	}
	
	/**
	 * execute registered worker
	 * @param unknown_type $yaml
	 * @return unknown
	 */
	function execute($yaml=""){
		/*** #pass */
		$automator = new Automator();
		if(!empty($yaml)) {
			$yamldata = Spyc::YAMLLoad($yaml);
			Rhaco::setVariable("conveyaml__yaml",$yamldata['plugins']);
		}
		$yaml = Rhaco::getVariable("conveyaml__yaml");

		while($plugin = array_shift($yaml)){
			$name = $plugin['module'];
			$automator->add("Conveyaml::toRss");
			$automator->add("Conveyaml::setVariable");
			$automator->add(Conveyaml::importpath($name)."::execute");
		}
		return $automator->execute();
	}
	
	/**
	 * set configuration with Rhaco::setVariable
	 * @param unknown_type $rss
	 * @return unknown
	 */
	function setVariable ($rss) {
		/*** #pass */
		$yaml = str_replace(array("\\r","\\n","\\\""),array("\r","\n","\""),Rhaco::getVariable(strtolower("conveyaml__yaml")));

		$plugin = array_shift($yaml);
		$modulename = $plugin['module'];
		if($pos = strrpos($modulename,'.')) $pos++;
		$modulename = strtolower(substr($modulename,$pos));

		if(!empty($plugin['config'])){
			foreach($plugin['config'] as $configname => $configvalue){
				if(!empty($configvalue) && !is_array($configvalue)) Rhaco::setVariable($modulename."__".$configname, $configvalue);
			}
		}
		Rhaco::setVariable("conveyaml__yaml", $yaml);
		return $rss;
	}
	
	/**
	 * force return variable to rss
	 *
	 * @param unknown_type $rss
	 * @return rss20 $rss
	 */
	function toRss($rss){
		/***
		 * $rss = Conveyaml::toRss("hoge");
		 * neq("hoge",$rss);
		 * assert(Variable::istype("Rss20",$rss));
		 * $rss = Conveyaml::toRss(new FileUtil());
		 * neq(new FileUtil(),$rss);
		 * assert(Variable::istype("Rss20",$rss));
		 * $rssfrom = new Rss20();
		 * $rssto = Conveyaml::toRss($rssfrom);
		 * eq($rssfrom,$rssto);
		 */
		if(!Variable::istype("Rss20",$rss)){
			return new Rss20();
		}
		return $rss;
	}
	
	/**
	 * return path based on worker path
	 *
	 * @param string $path
	 * @return string path
	 */
	function path($path=""){
		/***
		 * $path = Conveyaml::path("hoge/fuga");
		 * if(Rhaco::constant("WORKER_PATH")){
		 *  eq(FileUtil::path(Rhaco::constant("WORKER_PATH"),"hoge/fuga"),$path);
		 * }else{
		 *  eq(FileUtil::path(Rhaco::lib("worker"),"hoge/fuga"),$path);
		 * }
		 */
		return FileUtil::path(Rhaco::constant("WORKER_PATH",Rhaco::lib("workers")),$path);
	}
	
	/**
	 * return url based on worker url
	 *
	 * @return string url
	 */
	function url(){
		/***
		 * $url = Conveyaml::url();
		 * if(Rhaco::constant("WORKER_URL")){
		 *  eq(Rhaco::constant("WORKER_URL"),$url);
		 * }else{
		 *  eq(Rhaco::url(),$url);
		 * }
		 */
		return Rhaco::constant("WORKER_URL",Rhaco::url());
	}
	
	/**
	 * convert worker's class name to path
	 *
	 * @param unknown_type $class
	 * @return unknown
	 */
	function importpath($class){
		/***
		 * $path = Conveyaml::importpath("hoge.fuga");
		 * if(Rhaco::constant("WORKER_PATH")){
		 *  eq(FileUtil::path(Rhaco::constant("WORKER_PATH"),"hoge/fuga.php"),$path);
		 * }else{
		 *  eq(FileUtil::path(Rhaco::lib("worker"),"hoge/fuga.php"),$path);
		 * }
		 */
		return Conveyaml::path(str_replace(".","/",$class).".php");
	}
}
?>