<?php
/**
 * Filter::Lou
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */

Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import('tag.model.SimpleTag');
Rhaco::import('network.http.Http');

/**
 *
 * FilterLou
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterLou extends FilterBase{
    function execute($rss20)
    {
        $channel = $rss20->getChannel();
        $items = $rss20->getItem();

        $rss20_filtered = new Rss20();
        $rss20_filtered->channel = $channel;

        foreach ($items as $item) {
            $lou = $this->translateLou($item->getDescription());
            $item->setDescription($lou);
            $rss20_filtered->setItem($item);
        }

        return $rss20_filtered;
    }

    /**
     * translateLou
     *
     * @access protected
     * @param string $message
     */
    function translateLou($message)
    {
        $param = array(
            'v' => $this->variable('v'),
            'text' => $message,
        );
        $result = Http::post('http://lou5.jp/#text', $param);
        $tag = new SimpleTag();
        $tag->set($result, 'body');
        foreach($tag->getIn('p') as $p){
            if($p->getParameter('class') == 'large align-left box')
                return $p->getValue();
        }
        sleep(0.5);
        return $message;
    }

    function description()
    {
        return "Feedの内容を話題のルー語に変換";
    }

    function config(){
        return array(
            'v' => array('装飾', 'select', array(1 => '色やフォントはそのまま', 2 => '色やフォントもルーブログぽく'), true),
        );
    }
}
?>
