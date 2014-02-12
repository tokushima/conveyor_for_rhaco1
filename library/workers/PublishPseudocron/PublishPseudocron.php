<?php
Rhaco::import("model.PublishBase");
Rhaco::import("tag.HtmlParser");
Rhaco::import("lang.StringUtil");

/**
 * PublishPseudocron
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PublishPseudocron extends PublishBase{
	function PublishPseudocron(){
		$this->__init__(__FILE__);
	}
	function execute($rss){
		switch($this->variable("post")) {
			case 'all':
				$post = array($rss->get());
				break;
			case 'each':
				$post = array();
				foreach($rss->getItem() as $item){
					$eachrss = new Rss20();
					$eachrss->setItem($item);
					$post[] = $eachrss->get();
				}
				//シェルの場合はpostの内容を引数にする
				break;
			default:
				break;
		}
		switch($this->variable("type")){
			case 'web':
				$parser		= new HtmlParser();
				if(!empty($post)) $parser->setVariable("post",$post);
				$parser->setVariable("cron",$this);		
				$parser->write($this->template("output.html",__FILE__));
				break;
			case 'linux':
			case 'unix':
				$commands[0] = FileUtil::path(dirname(__FILE__), "pseudocron.sh");
				$commands[1] = FileUtil::path(Rhaco::constant("PUBLISH_PATH"),$this->variable("file").".php");
				if(!FileUtil::isFile($commands[1])){
					echo 'File not found '.$commands[1];
					return $rss;
				}
				$commands[2] = $this->variable("time");
				$commands[3] = $this->variable("finish");
				if(in_array($commands[3],array("specified","wholespecified"))) $commands[4] = $this->variable("loop");
				$command = implode(' ',$commands);
				if(!empty($post)) $command .= ' "'.implode('" "',$post).'"';
				system($command." &");
				break;
			case 'msdos':
				$commands[0] = FileUtil::path(dirname(__FILE__), "pseudocron.bat");
				$commands[1] = FileUtil::path(Rhaco::constant("PUBLISH_PATH"),$this->variable("file").".php");
				if(!FileUtil::isFile($commands[1])){
					echo 'File not found '.$commands[1];
					return $rss;
				}
				$commands[2] = $this->variable("time");
				$commands[3] = $this->variable("finish");
				if(in_array($commands[3],array("specified","wholespecified"))) $commands[4] = $this->variable("loop");
				$command = implode(' ',$commands);
				if(!empty($post)) $command .= ' "'.implode('" "',$post).'"';
				system(StringUtil::encode($command,"SJIS"));
				break;
						}
		return $rss;
	}
	function description(){
		return "指定ラインを擬似cronで動かす";
	}
	function config(){
		return array(
				"file"=>"実行するライン名",
				"type"=>array("ジョブの実行タイプ","select",array("web"=>"ブラウザから実行","linux"=>"シェルで実行(linux)","unix"=>"シェルで実行(unix)","msdos"=>"シェルで実行(MS-DOS)")),
				"post"=>array("送信するデータ","select",array("all"=>"全rssデータ","each"=>"rssの1アイテムずつ","none"=>"送信しない")),
				"finish"=>array("終了タイミング","select",array("none"=>"無し","wholespecified"=>"指定回数の全データ送信","specified"=>"指定回数")),
				"loop"=>"指定回数(回数を選択する指定をした場合)",
				"time"=>"実行周期(秒指定）"
				);
	}
	function rhacover() {
    	return "1.4.0";
    }
}
?>