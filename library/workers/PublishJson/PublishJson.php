<?php
Rhaco::import('model.PublishBase');
Rhaco::import('lang.Variable');
Rhaco::import('tag.model.SimpleTag');

class PublishJson extends PublishBase
{
    function execute($rss){
        $publish = array();
        $publish['channel'] = $rss->getChannel();
        $publish['items'] = $rss->getItem();

        print SimpleTag::getCdata(Variable::toJson($publish));
        return $rss;
    }
}

