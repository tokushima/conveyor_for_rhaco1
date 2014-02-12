<?php
Rhaco::import('model.FilterBase');

class FilterDecodeNumericentity extends FilterBase
{
    function execute($rss){
        $filter = array_map('trim', explode("\n", $this->variable('filter')));
        $items = $rss->getItem();

        $rss20_filtered = new Rss20();
        $rss20_filtered->channel = $rss->getChannel();

        foreach($items as $item){
            foreach($filter as $f){
                if($f == '.' || preg_match('/' . preg_quote($f, '/') . '/i', $item->getLink())){
                    $item->setDescription(mb_decode_numericentity($item->getDescription(), array(0, 0xffff, 0, 0xffff), 'utf-8'));
                    $item->setTitle(mb_decode_numericentity($item->getTitle(), array(0, 0xffff, 0, 0xffff), 'utf-8'));
                }
            }
            $rss20_filtered->setItem($item);
        }

        return $rss20_filtered;
    }

    function description(){
        return '数値参照を元に戻す';
    }

    function config(){
        return array('filter' => array('変換対象になるURL(.で全てを対象にします)', 'textarea', 'twitter.com', true));
    }
}

