<?php
/**
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
require_once("__init__.php");
Rhaco::import("io.FileUtil");
Rhaco::import("network.http.Header");
Rhaco::import("generic.Flow");
Rhaco::import("Conveyaml");

$flow = new Flow();
$io = new FileUtil();

$packed = "";
//if($flow->isPost()) {
	foreach($flow->getVariable() as $worker) {
		if(preg_match('@([^/]*?)/\1\.php$@',$worker)) {
			// directory
			$workerdir = Conveyaml::path();
			foreach($io->ls(Conveyaml::path(dirname($worker)),true) as $file){
				$filepath = StringUtil::substring($file->getFullName(),StringUtil::strlen($workerdir));
//				$packed = $io->pack(array($filepath=>$file->getFullName()));
//				foreach(StringUtil::strsplit($packed,10000) as $splitted){
//					echo($splitted);	
//				}
//				echo($io->pack(array($filepath=>$file->getFullName())));
				$packed .= $io->pack(array($filepath=>$file->getFullName()));
			}
		}else{
			//source file
//			$packed = $io->pack(array($worker=>Conveyaml::path($worker)));
//			foreach(StringUtil::strsplit($packed,10000) as $splitted){
//				echo($splitted);	
//			}
//			echo($io->pack(array($worker=>Conveyaml::path($worker))));
			$packed .= $io->pack(array($worker=>Conveyaml::path($worker)));
		}
	}
//}
Header::write(array("Content-Length"=>strlen($packed)));
print($packed);
?>