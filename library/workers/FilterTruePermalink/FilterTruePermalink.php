<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("network.http.Browser");
Rhaco::import("lang.StringUtil");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("io.FileUtil");
Rhaco::import("abbr.V");
/**
 *
 * FilterTruePermalink
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterTruePermalink extends FilterBase{
    
	function FilterTruePermalink(){
		$this->__init__(__FILE__);
	}
	
    function execute($rss20)
    {
        $channel = $rss20->getChannel();
        $items = $rss20->getItem();
        $channel->itemList = null;
        $rss20_filtered = new Rss20();
        $rss20_filtered->setVersion($rss20->getVersion());
        $rss20_filtered->setChannel($channel);
        $browser = new Browser();
        $fp = new FileUtil();
        $files = $fp->find('@\.yaml$@',dirname(__FILE__));
        foreach($files as $file) {
			$configs[] = Spyc::YAMLLoad($file->fullname);
        }
       foreach($configs as $key=>$config) {
       	if(empty($config['search']) && !empty($config['rewrite'])) {
       		if(preg_match('@(.)[a-zA-Z]*$@',$config['rewrite'],$match)) {
       			$dummy = explode($match[1],$config['rewrite']);
       			$configs[$key]['search'] = $dummy[1];
       			$configs[$key]['rewrite'] = $dummy[2];
       		}
       	}
       }
       
        foreach($items as $item) {
        	foreach($configs as $config) {
	   			if(!empty($config['match'])) {
	   				$link = $item->getLink();
	   				$source = $item->getSource();
		   			if(preg_match('@'.$config['match'].'@',$link)) {
		   				if(!empty($config['search'])) {
			   				$item->link = urldecode(preg_replace('@'.$config['search'].'@',$config['rewrite'],$item->link));
			   				$item->enclosure = urldecode(preg_replace('@'.$config['search'].'@',$config['rewrite'],$item->enclosure));
		   				}elseif(!empty($config['replace'])){
		   					$replace = $config['replace'];
		   					if(isset($item->$replace)) {
		   						$replace_method = "get".ucfirst($replace);
		   						$item->setLink(call_user_func(array(&$item,$replace_method)));
		   					}else{
		   						if(!V::istype("RssSource",$source)) continue;
	   							$url = $source->getUrl();
		   						$key = md5($url);
		   						if(!isset($tag[$key])) {
		   							$page = StringUtil::encode($browser->get($url));
		   							$tag[$key] = new SimpleTag();
		   							$tag[$key]->set($page);
		   							if($tag[$key]->getIn("item")){
			   							$tag_items[$key] = $tag[$key]->getIn("item");
		   							}elseif($tag[$key]->getIn("entry")){
			   							$tag_items[$key] = $tag[$key]->getIn("entry");
		   							}
		   						}
		   						if(isset($items[$key]) && sizeof($items[$key]) > 0){	
		   						foreach($tag_items[$key] as $tag_item) {
		   							$tag_link = $tag_item->getInValue("link");
		   							if(!$tag_link){
		   								$dummy = $tag_item->getIn("link");
		   								$tag_link = $dummy[0]->getParameter("href");
		   							}
		   							if($tag_link===$item->link) {
		   								if($tag_item->getInValue($config['replace'])){
		   									$item->setLink($tag_item->getInValue($config['replace']));
		   								}
		   								break;
		   							}
		   						}
		   						}
		   					}
		   				}
		   			}
	   			}else{
	   				$item->setLink(urldecode(preg_replace('@'.$config['search'].'@',$config['rewrite'],$item->getLink())));
	   				$enclosure = $item->getEnclosure();
	   				$enclosure->setUrl(urldecode(preg_replace('@'.$config['search'].'@',$config['rewrite'],$enclosure->getUrl())));
	   				$item->setEnclosure($enclosure);
	   			}
        	}
        	$rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "The worker convert redirect link to its permalink.";
    }

    function rhacover() {
    	return "1.3.0";
    }

    function testConfigs(){
    	return array(
    		array('in'=>FeedParser::parse(
<<< __TEST__
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="../../../../../css/rss/feedRss2.xsl" media="screen" type="text/xsl"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd" version="2.0">  
  <channel> 
    <title>TruePermalink Test</title>  
    <link>http://blog.shigepon.com/</link>  
    <description>TruePermalinkテスト</description>  
    <language>ja</language>  
    <item> 
      <link>http://feeds.feedburner.jp/~r/shigepon/~3/3180007/</link>  
      <title>TruePermalinkテスト</title>  
      <pubDate>Mon, 15 Dec 2008 18:52:29 +0900</pubDate>  
      <description><![CDATA[<p>テスト</p>]]></description>  
      <category>test</category>  
      <author>shigepon</author> 
      <guid isPermaLink="false">http://blog.shigepon.com/snippet0</guid>
      </item>
  </channel> 
</rss>
__TEST__
)    		,'out'=>FeedParser::parse(
<<< __TEST__
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="../../../../../css/rss/feedRss2.xsl" media="screen" type="text/xsl"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd" version="2.0">  
  <channel> 
    <title>TruePermalink Test</title>  
    <link>http://blog.shigepon.com/</link>  
    <description>TruePermalinkテスト</description>  
    <language>ja</language>  
    <item> 
      <link>http://blog.shigepon.com/snippet0</link>  
      <title>TruePermalinkテスト</title>  
      <pubDate>Mon, 15 Dec 2008 18:52:29 +0900</pubDate>  
      <description><![CDATA[<p>テスト</p>]]></description>  
      <category>test</category>  
      <author>shigepon</author> 
      <guid isPermaLink="false">http://blog.shigepon.com/snippet0</guid>
      </item>
  </channel> 
</rss>
__TEST__
)),
    		array('in'=>FeedParser::parse(
<<< __TEST__
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="../../../../../css/rss/feedRss2.xsl" media="screen" type="text/xsl"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd" version="2.0">  
  <channel> 
    <title>TruePermalink Test</title>  
    <link>http://blog.shigepon.com/</link>  
    <description>TruePermalinkテスト</description>  
    <language>ja</language>  
    <item> 
      <link>http://www.pheedo.jp/feeds/ht.php?t=c&amp;i=d824e959ea9c164add239c1d4b412131</link>  
      <title>TruePermalinkテスト</title>  
      <pubDate>Mon, 15 Dec 2008 18:52:29 +0900</pubDate>  
      <description><![CDATA[<p>テスト</p>]]></description>  
      <category>test</category>  
      <author>shigepon</author> 
      <pheedo:origLink>http://blog.shigepon.com/snippet1</pheedo:origLink>
      <guid isPermaLink="false">http://blog.shigepon.com/snippet0</guid>
      </item>
  </channel> 
</rss>
__TEST__
)    		,'out'=>FeedParser::parse(
<<< __TEST__
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="../../../../../css/rss/feedRss2.xsl" media="screen" type="text/xsl"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd" version="2.0">  
  <channel> 
    <title>TruePermalink Test</title>  
    <link>http://blog.shigepon.com/</link>  
    <description>TruePermalinkテスト</description>  
    <language>ja</language>  
    <item> 
      <link>http://blog.shigepon.com/snippet0</link>  
      <title>TruePermalinkテスト</title>  
      <pubDate>Mon, 15 Dec 2008 18:52:29 +0900</pubDate>  
      <description><![CDATA[<p>テスト</p>]]></description>  
      <category>test</category>  
      <author>shigepon</author> 
      <guid isPermaLink="false">http://blog.shigepon.com/snippet0</guid>
      </item>
  </channel> 
</rss>
__TEST__
))
);
    }
    
}
?>
