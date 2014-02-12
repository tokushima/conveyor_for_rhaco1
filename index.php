<?php
/**
 * @author kazutaka tokushima
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
require_once("./__init__.php");
Rhaco::import("spyc");
Rhaco::import("io.FileUtil");
Rhaco::import("generic.Flow");
Rhaco::import("network.http.Header");
Rhaco::import("Conveyaml");
Rhaco::import("setup.util.ApplicationInstaller");
Rhaco::import("network.http.RequestLogin");
Rhaco::import("exception.ExceptionTrigger");
Rhaco::import("exception.model.GenericException");
Rhaco::import("exception.model.NotFoundException");
Rhaco::import("exception.model.RequireException");
Rhaco::import("lang.Variable");


if(Variable::bool(Rhaco::constant("IS_LOGIN"))) RequestLogin::loginRequired();


$path = Rhaco::constant("PUBLISH_PATH",Rhaco::path("publish"));
$flow = new Flow();
$flow->setVariable("loadmsg",Message::_("Loading..."));

if($flow->isPost() && $flow->isVariable("actionname")){
	$io = new FileUtil();

	if($flow->getVariable("action") == "load") {
		if(preg_match('@<<< __YAML__(.*?)__YAML__@ms',$io->read($path."/".$flow->getVariable("actionname").".php"),$match)){
			$modules = Spyc::YAMLLoad($match[1]);
		}else{
			$modules = array("plugins"=>array(0));
		}
		foreach($modules["plugins"] as $mkey=>$mvar){
			$list = ArrayUtil::arrays(explode(".",$mvar["module"]),-2,2,true);
			if(Variable::iequal($list[0],$list[1])){ $modules["plugins"][$mkey]["module"] = implode(".",ArrayUtil::arrays(explode(".",$mvar["module"]),0,-1));}
		}
		header("Content-type: text/json; charset=utf-8");
		echo str_replace(array("\n","\r"),array("\\n","\\r"),Variable::toJson($modules));
	}else{
		$install = true;
		$configs = ArrayUtil::arrays($flow->getVariable("variables"));
		$modules = array();

		foreach(ArrayUtil::arrays($flow->getVariable("usemodule")) as $module){
			if(!empty($module)){
				$obj = Rhaco::obj(Conveyaml::importpath($module));
				$loadconfigs = $obj->getConfig();
				if($request = $obj->getRequired()){
					$install = false;

					foreach($request as $name=>$var) {
						ExceptionTrigger::raise(new NotFoundException($name));
					}
				}
				if(empty($configs[$module]) || strlen(key($configs[$module])) < 1) {
					$modules[]['module'] = $module;
				}else{
					$config = array();
					foreach($configs[$module] as $config_name => $config_array){
						$chkconfig = trim($config_array[0]);
						if($loadconfigs[$config_name][3] && empty($chkconfig)) {
							$install = false;
							ExceptionTrigger::raise(new RequireException($loadconfigs[$config_name][0]));
						}else{
							$config[$config_name] = StringUtil::toULD(array_shift($configs[$module][$config_name]));
						}
					}
					$modules[] = array('module'=>$module, 'config'=>$config);
				}
			}
		}
		if($install) {
			ApplicationInstaller::writeInitFile($path);
			$flow->setVariable("yaml",Spyc::YAMLDump(array('plugins'=>$modules)));
			$parser = $flow->parser("action.template");
			$io->write($io->path($path,$flow->getVariable("actionname").".php"),ApplicationInstaller::getPhp($parser->read()));
		}else{
			ExceptionTrigger::raise(new GenericException("Line {1} was not installed.",array($flow->getVariable("actionname"))));
		}
	}
}else{
	if($flow->getVariable("action") == "loadModule"){
		$list = array();
		$loadmodules = Conveyaml::loadModule();
		
		if(!empty($loadmodules)){
			foreach(array('subscription','filter','publish') as $type){
				if(!empty($loadmodules[$type])){
					foreach($loadmodules[$type] as $name=>$obj){
						$config = $obj->getConfig();
						$configs = array();

						foreach($config as $pkey=>$property){
							switch($property[1]){
								case 'text':
									$property[1] = 'textfield';
									break;
								case 'select':
									$property[1] = 'combo';
									$dummy = $property[2];
									$property[2] = array();
									foreach($dummy as $dkey=>$dvar){
										$property[2][] = array($dkey,$dvar);
									}
									break;
							}
							$configs[] = array("key"=>"$pkey","name"=>"variables[{$obj->path}][$pkey][]","fieldLabel"=>$property[0],"xtype"=>$property[1],"value"=>$property[2]);
							
						}
						$list[] = array("name"=>$name,"value"=>$obj->path,"description"=>$obj->getDescription(),"type"=>$type,"config"=>$configs);
					}
				}
			}
		}
		$json = array("modules"=>$list);
		header("Content-type: text/json; charset=utf-8");
		echo str_replace(array("\n","\r"),array("\\n","\\r"),Variable::toJson($json));
	}else if($flow->getVariable("action") == 'loadPublish'){
		header("Content-type: text/json; charset=utf-8");
		$list = array();
		if($flist = F::find('@\.php$@i',Rhaco::constant('PUBLISH_PATH'))){
			foreach($flist as $file){
				if(method_exists($file,"getOriginalName") && $file->getOriginalName()!='__init__'){
					$list[] = array('linename'=>$file->getOriginalName());
				}
			}
		}
		echo Variable::toJson(array('lines'=>$list));
	}else{
		$theme = Rhaco::constant('EXT_THEME');
		$flow->setVariable('theme',$theme);
		switch ($theme) {
			case 'xtheme-slate':
			$flow->setVariable('logo','logo_slate');
			break;
			
			default:
			$flow->setVariable('logo','');
			break;
		}
		
		$flow->write("index.html");
	}
}
?>