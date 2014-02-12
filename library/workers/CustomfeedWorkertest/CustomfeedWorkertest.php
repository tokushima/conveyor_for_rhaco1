<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.Assert");
Rhaco::import("io.FileUtil");

/**
 * CustomfeedWorkertest
 * workerのテスト用worker
 * 
 * 使用法:
 * conveyorでsubscriptionとして使用する。
 * 各workerでtestConfigs, およびextraTestsメソッドを持つものを実行する
 * testConfigsにはworkerの設定、入力rss、出力すべきrssを配列で指定する
 * 例:
	function testConfigs(){
		return	array(array(
				'config'=>array('url'=>'http://blog.shigepon.com/'),
				'in'=>'',
				'out'=>FeedParser::read('http://blog.shigepon.com/')
			));
	}
 * 
 * extraTestsにはそれ以外で行いたいテストを書く。引数としてassertクラスを取る。
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 *  */
class CustomfeedWorkertest extends SubscriptionBase {
	function execute($rss){
		$rss20 = new Rss20();
		$rss20->setChannel("Workertest",
			"Workertest",
			"",
			"ja"
		);
		$dir = Rhaco::constant("WORKER_PATH");
		if(is_dir($dir)){
			foreach(FileUtil::find('@\.php$@i',$dir,true) as $file){
				if(strpos($file->getFullname(),"/_") === false
					&& preg_match('@/([a-zA-Z0-9_-]+)/\1\.php$@i',$file->getFullname())
				){
					L::deep_debug("Testing ".$file->getFullname());
					$this->test($file->getFullname());
					//TODO:見せ方が適当なので、検討中。意見募集。
					$description = "";
					$description.="Success:".AssertResult::count(AssertResult::typeSuccess())."\n";
					$description.="Fail:".AssertResult::count(AssertResult::typeFail())."\n";
					$description.="Pass:".AssertResult::count(AssertResult::typePass())."\n";
					$description.="Viewing:".AssertResult::count(AssertResult::typeViewing())."\n";
					$description.="None:".AssertResult::count(AssertResult::typeNone())."\n";
					foreach(AssertResult::results() as $result){
					$description.=$result->getTypeString()."\n";
					$description.="Line:".$result->getLine()."\n";
					$description.=$result->getResult()."\n";
					}
					AssertResult::clear();
					$rss20->setItem($file->getName(),$description);
				}
			}
		}
		return $rss20;
	}
	function test($filename){
		$assert = new Assert();
		$worker = Rhaco::obj($filename);
		if(method_exists($worker,"testConfigs") || method_exists($worker,"extraTests")){
			if(method_exists($worker,"testConfigs")){
			foreach($worker->testConfigs() as $test){
				foreach($test['config'] as $key=>$var){
					Rhaco::setVariable(strtolower(get_class($worker)).'__'.$key,$var);
				}
				$result = $worker->execute($test['in']);
				$assert->assertEquals($test['out'],$result,"Rss input output test."," ","execute",get_class($worker));
			}
			}
			if(method_exists($worker,"extraTests")){
				$worker->extraTests($assert);
			}
		}else{
			$assert->none("","","",get_class($worker));
		}
	}
	function description(){
		return "Workerのテストを行う";
	}
}
?>