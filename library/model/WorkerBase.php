<?php
Rhaco::import("tag.feed.Rss20");
Rhaco::import("resources.Message");
Rhaco::import("lang.Variable");
Rhaco::import("lang.ArrayUtil");
/**
 * @author kazutaka tokushima
 * @author SHIGETA Takeshiro
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class WorkerBase{
	var $path;

	/**
	 * 説明
	 * @param void
	 * @return string
	 */
	function description(){
		return "";
	}
	/**
	 * フォーム定義
	 * @param void
	 * @return array
	 */
	function config(){
		return array();
		// return array("variable_name"=>array("変数の説明","(text/textarea/password/select)","デフォルト値",必須(bool));
	}
	/**
	 * 必須ライブラリ
	 * @param void
	 * @return array
	 */
	function required(){
		return array();
		// return array("Xmlpath","http://www.rhaco.org/rhacolibs/tag/Xmlpath.php");
	}
	
	/**
	 * specify rhaco version for worker
	 * @param void
	 * @return string
	 */
	function rhacover(){
		return "";	
	}
	
	/**
	 * initialize worker including gettext message.
	 * @param __FILE__ $base
	 * @return unknown
	 */
	function __init__($base=null){
		Message::includeMessages(dirname($base));
	}
	
	/**
	 * plugin配下のテンプレートのパスを取得する
	 *
	 * @param string $filename
	 * @param __FILE__ $base
	 * @return unknown
	 */
	function template($filename,$base=null){
		/***
		 * eq('/hoge/templates/file.php',WorkerBase::template('file.php','/hoge/fuga'));
		 */
		return FileUtil::path(dirname($base),"/templates/".$filename);
	}

	/**
	 * pluginに割り当てられた変数を取得する
	 * @param string $variable
	 * @return string
	 */
	function variable($variable){
		/***
		 * $wk = new WorkerBase();
		 * Rhaco::setVariable("workerbase__framework","rhaco");
		 * Rhaco::setVariable("filterbase__rhaco","frog");
		 * eq("rhaco",$wk->variable("framework"));
		 * neq("frog",$wk->variable("rhaco"));
		 */
		return str_replace(array("\\r","\\n","\\\""),
							array("\r","\n","\""),
							Rhaco::getVariable(strtolower(get_class($this)."__".$variable)));
	}

	/**
	 * get configuration description array
	 * @param void
	 * @return array
	 */
	function getConfig(){
		/*** #pass */
		$list = array();
		
		foreach(ArrayUtil::arrays($this->config()) as $name => $conf){
			$conf = ArrayUtil::arrays($conf,0,4,true);
			$conf[0] = Message::_n($conf[0]);
			$conf[1] = strtolower($conf[1]);
			$conf[1] = ($conf[1] == "password" || $conf[1] == "textarea" || $conf[1] == "select") ? $conf[1] : "text";
			$conf[2] = ($conf[1] == "select") ? array_map(array("Message","_n"),ArrayUtil::arrays($conf[2])) : Message::_n(print_r($conf[2],true));
			$conf[3] = Variable::bool($conf[3]);
			$list[$name] = $conf;
		}
		return $list;
	}
	
	/**
	 * check if required libraries are installed 
	 * and return them which are not installed yet.
	 * @param void
	 * @return unknown
	 */
	function getRequired(){
		/*** #pass */
		$list = array();
		foreach(ArrayUtil::arrays($this->required()) as $path => $require){
			$require = ArrayUtil::arrays($require,0,2,true);
			if(!Rhaco::import($path) && !Rhaco::import(FileUtil::path(Rhaco::constant("WORKER_PATH"),str_replace(".","/",$path).".php"),$require[0])) $list[$path] = $require;
		}
		return $list;
	}
	
	/**
	 * execute worker (in the class, process is dummy)
	 *
	 * @param unknown_type $variable
	 * @return void
	 */
	function execute($variable){
		unset($variable);
	}
	
	/**
	 * check if variable is type of Rss20 object.
	 *
	 * @param unknown_type $variable
	 * @return boolean
	 */
	function verify($variable){
		/***
		 * assert(WorkerBase::verify(new Rss20()));
		 * eq(false,WorkerBase::verify(new Rss()));
		 */
		return (Variable::istype("Rss20",$variable));
	}
	
	/**
	 * get description of the worker
	 * @param void
	 * @return unknown
	 */
	function getDescription(){
		/*** #pass */
		return Message::_n($this->description());
	}
	
	/**
	 * set real path of the worker
	 * @param string $path
	 */
	function setRealpath($path){
		/***
		 * $wk = new WorkerBase();
		 * $wk->setRealpath(F::path(Rhaco::constant("WORKER_PATH"),"hoge/hoge.php"));
		 * eq("hoge.hoge",$wk->path);
		 * eq("hoge.hoge",$wk->path()); //test of path()
		 */
		$this->path = substr(str_replace("/",".",str_replace(FileUtil::path(Rhaco::constant("WORKER_PATH")),"",$path)),0,-4);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function label(){
		$list = explode(".",$this->path);
		return (sizeof($list) > 1 && Variable::iequal(ArrayUtil::implode($list,"",-2,1),get_class($this))) ? 
					ArrayUtil::implode($list,".",0,-1) : $this->path;
	}
	
	/**
	 * return path of worker (test is included in setRealPath)
	 * @param void
	 * @return string $this->path
	 */
	function path(){
		return $this->path;
	}
}
?>