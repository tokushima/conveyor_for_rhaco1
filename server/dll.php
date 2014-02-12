<?php
/**
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
require_once('__init__.php');
Rhaco::import('io.FileUtil');
Rhaco::import('network.http.Header');
Rhaco::import('generic.Flow');

$flow = new Flow();
$io = new FileUtil();

$packed = '';
switch ($flow->getVariable('type')){
	case 'worker':
		$path = Rhaco::constant('WORKER_PATH',Rhaco::lib("workers"));
		$restriction = '@([^/]*?)/\1\.php$@';
		break;
	case 'line':
		$path = Rhaco::constant('PUBLISH_PATH',Rhaco::path("publish"));
		$restriction = '';
		break;
	default:
		break;
}
//var_dump($flow->getVariable('target'));
foreach($flow->getVariable('target') as $target) {
//	echo $target;
//	echo $restriction;
//	echo FileUtil::path($path,$target);
//	$target = str_replace(array('./','../'),'',$target);
	if(!empty($restriction)){
		if(preg_match($restriction,$target)) {
			foreach($io->ls(dirname(FileUtil::path($path,$target)),true) as $file){
				$filepath = StringUtil::substring($file->getFullName(),StringUtil::strlen($path));
				$packed .= $io->pack(array($filepath=>$file->getFullName()));
//				var_dump($file,$packed);
			}
		}
	}else{
		//source file
		$packed .= $io->pack(array($target=>FileUtil::path($path,$target)));
	}
}//var_dump($flow->getVariable("target"));
Header::write(array('Content-Length'=>strlen($packed)));
print($packed);
?>
