<?php
/**
 * CustomFeed::Mixi::Footprint
 *
 * @author TSURUOKA Naoya <tsuruoka@php.net>
 */

Rhaco::import("SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("tag.feed.FeedParser");
Rhaco::import("tag.feed.Rss20");

/**
 * ScrapingGoogleImageSearch
 *
 * @author TSURUOKA Naoya <tsuruoka@php.net>
 */
class ScrapingGoogleImageSearch extends SubscriptionBase{

    /**
     * fetch
     *
     * @access private
     */
    function fetch($word)
    {
        $image_list = array();
        $search_url = "http://images.google.co.jp/images?svnum=10&hl=ja&hs=zKK&ie=UTF-8&q=";
        $search_url = $search_url . urlencode($word);
        $link = "http://images.google.co.jp/images?q=tbn:";

        $html = file_get_contents($search_url);

        $pattern = '|dyn\.Img\("(.*?)"\);|s';
        preg_match_all($pattern, $html, $matches);

        foreach ($matches[1] as $word) {
            $list = explode(",", $word);
            $image_link = $link . trim($list[2], '"') . ':' . trim($list[3], '"');
            $trim_filename = create_function(
                '$string',
                'return trim(str_replace(array("\"", ":", "*", "?"), "",
                    strip_tags($string)), " .");'
            );
            $image_list[] = array(
                "url" => $image_link,
                "filename" => $trim_filename("{$list[6]}.{$list[10]}")
            );
        }

        return $image_list;
    }

    function getFilenameExt($filename)
    {
        $parts = explode(".", $filename);
        return end($parts);
    }

    /**
     * execute
     *
     * @access public
     */
    function execute(){

        $image_list = $this->fetch($this->define('word'));

        $rss20 = new Rss20();
        $rss20->setChannel(
            'ScrapingGoogleImageSearch',
            'description',
            'http://www.google.com/',
            'ja'
        );

        foreach ($image_list as $image) {

            $item = new RssItem20($image['filename'], $image['filename'], $image['url']);

            if (isset($image['url'])) {
                $enclosure = new RssEnclosure($image['url'], "image/" . $this->getFilenameExt($image['filename']));
                $item->setEnclosure($enclosure);
            }

            $rss20->setItem($item);

        }

        return $rss20;

    }

    /**
     * description
     *
     * @access public
     */
    function description(){
        return "Googleイメージ検索の結果を取得する。";
    }

    /**
     * config
     *
     * @access public
     */
    function config(){
        $config = array(
            'search_word' => '検索ワードを指定します。',
            );
        return $config;
    }
}
?>
