<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("lang.StringUtil");
Rhaco::import("network.http.Browser");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("tag.feed.Rss20");
/**
 * CustomFeedKanpo
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class CustomfeedKanpo extends SubscriptionBase{
    function execute($variable){
        $rss20 = new Rss20();
        $rss20->setChannel("KANPO",
            "description",
            "http://kanpou.npb.go.jp/html/contents.html",
            "ja"
        );
        $source = new RssSource("http://kanpou.npb.go.jp/html/contents.html","官報");

		$browser = new Browser();
    	$page = StringUtil::encode($browser->get("http://kanpou.npb.go.jp/html/contents.html"));
		$tag = new SimpleTag();
		$tag->set($page);
		$counter = 0;
		foreach($tag->getIn("p") as $key => $p_tag){
				foreach($p_tag->getIn("a") as $link_tag) {
				$title = $link_tag->getValue();
				$link = $link_tag->getParameter("href");
				$clink = "http://kanpou.npb.go.jp/".substr(dirname($link),3)."/";
				$datestr = strtotime(substr($link,3,8));
				$link = "http://kanpou.npb.go.jp/html/".str_replace('f.html','.html',$link);
				$ctag = new SimpleTag();
				$cpage =  StringUtil::encode($browser->get($link));
    			$ctag->set($cpage);
    			$description = '';
    			if($this->variable("enclosure")){
     				$pagecount = 0;
    				$pages = array();
    				foreach($ctag->getIn("a") as $a_tag){
    					$href_check = $a_tag->getParameter("href");
    					break;
    				}
    				foreach($ctag->getIn("p") as $p2_tag) {
    					if($a_tags = $p2_tag->getIn("a")){
    						foreach($a_tags as $a2_tag){
    							$href = $a2_tag->getParameter("href");
    							break;
    						}
    						if($href != $href_check){
    							$href_check = $href;
    							$pagecount++;
    							$pref = preg_replace(array('@f\.html@','@^\.@'),array('.pdf','pdf'),$href);
    							$ppath = Url::parseAbsolute($clink,$pref);
    							if(empty($pages[$pagecount]["enclosure"])) $pages[$pagecount]["enclosure"]=new RssEnclosure($ppath,'pdf');
    						}
    						if(!isset($pages[$pagecount]['description'])) $pages[$pagecount]['description'] = '';
    						$pages[$pagecount]["description"].=preg_replace('@HREF=\".*?\"@', 'HREF="'.Url::parseAbsolute($clink,$href).'"',$p2_tag->getPlain());
    					}else{
    						if(!isset($pages[$pagecount]['description'])) $pages[$pagecount]['description'] = '';
    						$pages[$pagecount]["description"].=$p2_tag->getPlain();
    					}
	    			}//var_dump($pages);
	    			foreach($pages as $key=>$page){
    					$item = new RssItem20();
    					$no=$key+1;
	    				$item->setTitle($title.$no);
    					if(isset($page["description"])) $item->setDescription($page["description"]);
	    				$item->setPubDate($datestr);
    					$item->setLink($link);
	    				$item->setSource($source);
	    				if(isset($page["enclosure"])) $item->setEnclosure($page["enclosure"]);
	    				$rss20->setItem($item);
	    			}
    				
    			}else{
					$item = new RssItem20();
    				foreach($ctag->getIn("BLOCKQUOTE") as $block_tag) {
	    				$description .= $block_tag->getValue();
	    			}
	    			$description = str_replace('HREF="', 'HREF="'.$clink, $description);
	    			$item->setTitle($title);
	    			$item->setLink($link);
	    			$item->setDescription($description);
	    			$item->setPubDate($datestr);
	    			$item->setSource($source);
	    			$rss20->setItem($item);
	    			}
    			}
				if($counter++ > 0) break;
		}
		$rss20 = $this->merge($variable,$rss20);
        return $rss20;
    }
    function description(){
    	return "官報を取得する";
    }
    function config(){
        $config = array(
            "pages" => "収集ページ数",
            "enclosure" => array("PDFアドレスをenclosureに入れるか？","select",array("yes"=>"はい(フィードが分割されます)","no"=>"いいえ"))
        );

        return $config;
    }
    function rhacover() {
    	return "1.1.0";
    }
}

?>
