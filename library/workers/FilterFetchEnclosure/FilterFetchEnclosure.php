<?php
/**
 * Filter::FetchEnclosure
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */

Rhaco::import("FilterBase");
Rhaco::import("tag.feed.Rss20");

/**
 *
 * FilterFetchEnclosure
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterFetchEnclosure extends FilterBase {
    
    /**
     * execute
     *
     * @access public
     */
    function execute($rss20)
    {
        $items = $rss20->getItem();
        foreach ($items as $item) {
            $enclosure = $item->getEnclosure();
            $this->download($enclosure->getUrl(), $this->define('download_dir'));
        }
        return $rss20;
    }

    /**
     * download
     *
     * @access private
     */
    function download($url, $dir)
    {
        $binary = file_get_contents($url);
        file_put_contents($dir . '/' . basename($url), $binary);
    }

    /**
     * description
     *
     * @access public
     * @return string
     */
    function description()
    {
        return "Enclosureの値を取得する";
    }

    /**
     * config
     *
     * @access public
     */
    function config(){
        $config = array(
            'download_dir' => 'ダウンロード先のディレクトリを指定します。',
            );
        return $config;
    }
}
?>
