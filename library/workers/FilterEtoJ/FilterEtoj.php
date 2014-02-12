<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("network.http.Browser");
Rhaco::import("tag.model.SimpleTag");

/**
 *
 * FilterEtoJ
 *
 * @author SHIGETA Takeshiro
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterEtoj extends FilterBase{
    
    
    function execute($rss20)
    {
        $channel = $rss20->getChannel();
        $items =& $rss20->getItem();

        $rss20_filtered = new Rss20();
        $rss20_filtered->setChannel($channel->getTitle(),
            $channel->getDescription(),
            $channel->getLink(),
            "ja"
        );

        foreach ($items as $item) {
            if($channel->language == 'en' || $this->isEnglish($item->getDescription()) /*&& !$this->isCode($item->description)*/) {
 	            $e = $this->translateEtoj($item->description);
	            $item->setDescription($e);
	            $rss20_filtered->setItem($item);
            }else{
 	            $rss20_filtered->setItem($item);
            }
        }
//        echo "ETOJ";
//        var_dump($rss20_filtered);
        return $rss20_filtered;
    }
    
    function isEnglish ($message) {
    	return preg_match('/(\w+\s){10,}/m',$message);
//		return (strlen($message) == mb_strlen($message));
	}
    
    function isCode ($message) {
    	return  preg_match('/[0-9a-zA-Z".,%$(]+\s*[=]\s*[0-9a-zA-Z".,%$(]+\s/m',$message);
//		return (strlen($message) == mb_strlen($message));
	}

	function getSentence ($message) {
		$message = html_entity_decode($message);
		$message = strip_tags($message);
		return  substr($message,0,10000);
	}

    function translateEtoj($message)
    {
        $message = $this->getSentence($message);

        $url = "http://translate.livedoor.com/";

        $browser = new Browser();
        $request = $browser->get($url);
        $browser->setVariable('src_text', $message);
        $browser->setVariable('clear_flg', 1);
        $browser->setVariable('trns_type', "1,2");
        sleep(0.5);

        if (!$body = $browser->post($url)) {
            return false;
        }

        $pattern = '|<textarea name="tar_text.*?>(.*?)</textarea>|s';
        preg_match($pattern, $body, $matches);

        return trim($matches[1]);
    }

    
    function description()
    {
        return "Feedの内容が英語の場合日本語に変換";
    }

    function rhacover() {
    	return "1.1.0";
    }
}
?>
