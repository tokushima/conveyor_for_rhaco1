<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("io.FileUtil");
/**
 * FilterStripRssAd
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterStripRssAd extends FilterBase{
    
    function execute($rss20)
    {
        $channel = $rss20->getChannel();
        $items = $rss20->getItem();
        $channel->itemList = null;
        $rss20_filtered = new Rss20();
        $rss20_filtered->setVersion($rss20->getVersion());
        $rss20_filtered->setChannel($channel);
        $fp = new FileUtil();
        $files = $fp->find('@\.yaml$@',dirname(__FILE__));
        foreach($files as $file) {
			$configs[] = Spyc::YAMLLoad($file->fullname);
        }
        foreach($items as $item) {
        	$strip = false;
	       	foreach($configs as $config) {
		       	if(is_array($config)){
		       		$expression = array_map('trim',split('=',$config['condition'],2));
		       		if(!empty($config['strip'])) {
		       			if(sizeof($expression) >= 2 && preg_match('@'.$expression[1].'@',$item->$expression[0])) {
		       				$strip = true;
		       				continue;
		       			}
		       		}else{
		       			if(sizeof($expression) >= 2){
			       			$item->$expression[0] = preg_replace('@'.$expression[1].'@','',$item->$expression[0]);
		       			}
		       		}
		       	}
	       	}
        	if(!$strip) $rss20_filtered->setItem($item);
        }
        return $rss20_filtered;
    }
    
    function description() {
    	return "フィードに含まれる広告を削除する";
    }

    function testConfigs(){
    	return array(
    		array('in'=>FeedParser::parse(
<<< __TEST__
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="../../../../../css/rss/feedRss2.xsl" media="screen" type="text/xsl"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd" version="2.0">  
  <channel> 
    <title>Mozilla Re-Mix</title>  
    <link>http://mozilla-remix.seesaa.net/</link>  
    <description>爆発的に普及中のFirefoxやThunderbirdはWEBユーザーの必須アイテム。初心者からヘビーユーザーまで使える！便利でクールなFirefox拡張機能（アドオン）の使い方やMozilla関連情報をどうぞ。</description>  
    <language>ja</language>  
    <docs>http://blogs.law.harvard.edu/tech/rss</docs>  
    <itunes:subtitle/>  
    <itunes:summary>爆発的に普及中のFirefoxやThunderbirdはWEBユーザーの必須アイテム。 初心者からヘビーユーザーまで使える！便利でクールなFirefox拡張機能（アドオン）の使い方やMozilla関連情報をどうぞ。</itunes:summary>  
    <itunes:keywords>Firefox Thunderbird Mozilla モジラ 拡張機能 テーマ メールソフト タブブラウザ ブラウザ アドオン Thunderbird2 バックアップ カスタマイズ ブックマーク カスタマイズ Google Chrome about:config 高速化 windows ツールバー user.js Ubiquity Firefox 3</itunes:keywords>  
    <itunes:author>ERROR: NOT PERMITED METHOD: nickname</itunes:author>  
    <itunes:owner> 
      <itunes:name/>  
      <itunes:email/> 
    </itunes:owner>  
    <itunes:explicit>no</itunes:explicit>

    <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="self" href="http://rss.rssad.jp/rss/CG5kXk4HYq9M/rss_0002" type="application/rss+xml"/>  
    <item> 
      <link>http://mozilla-remix.seesaa.net/article/111273992.html</link>  
      <title>Firefoxのサイドバーでメモを取ることができるMIT製アドオン「list.it」</title>  
      <pubDate>Mon, 15 Dec 2008 18:52:29 +0900</pubDate>  
      <description><![CDATA[<p>ローカルやWebアプリには、「メモ」を効率よく管理するようなものがたくさんあり、Firefoxでも、それらツールの専用アドオンをインストールしておくことによって、メモの管理を行うことが可能です。代表的なものに、Google ノートブックやEvernote、などがありますが、今回は、MITの研究所で作成されたというメモ管理ツール「list.it」を試してみました。</p>
<br clear="all" /><a href="http://rss.rssad.jp/rss/ad/K24wTRxnZh3Y/v_JWg0xzbmjr?type=1" target="_blank"><img src="http://rss.rssad.jp/rss/img/K24wTRxnZh3Y/v_JWg0xzbmjr?type=1" border="0"/></a><br/>]]></description>  
      <content:encoded><![CDATA[
ローカルやWebアプリには、「メモ」を効率よく管理するようなものがたくさんあり、Firefoxでも、それらツールの専用アドオンをインストールしておくことによって、メモの管理を行うことが可能です。<br /><br />代表的なものに、Google ノートブックやEvernote、などがありますが、今回は、MITの研究所で作成されたというメモ管理ツール「list.it」を試してみました。<br /><br /><br /><a name="more"></a><a href="http://groups.csail.mit.edu/haystack/listit/" target="_blank">「list.it」</a>は,firefox 3以上のサイドバーで動作するメモ帳で、テキストやリンク、画像などを気ままに追加して後で検索、参照することができるというごくシンプルなアドオンです。<br /><br />アドオンを入手するには、最初にlist.itのサイトでユーザー登録を行う必要があります。<br /><br />ページ内のget it!以下で、step 2: get it (and sign up!)をクリックし、登録用のメールアドレス、パスワードを入力し、[Register me]ボタンをクリックすれば登録確認メールが届きます。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/00_list_it_Firefox_Add-ons.JPG" alt="00_list_it_Firefox_Add-ons.JPG" width="375" height="425" border="0" /><br /><br /><br />届いたメール内下部に記載されている長いリンクをクリックすれば確認が完了し、Firefoxにアドオンのダウンロードリンクが表示されます。<br /><br />＊[Configure it!]ボタンは無視してかまいません。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/01_list_it_Firefox_Add-ons.JPG" alt="01_list_it_Firefox_Add-ons.JPG" width="510" height="441" border="0" /><br /><br /><br />アドオンをインストールして再起動すると、サイドバー表示項目に「list.it」が加わり、表示すると以下のようにかなりシンプルな画面が現れます。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/02_list_it_Firefox_Add-ons.JPG" alt="02_list_it_Firefox_Add-ons.JPG" width="255" height="293" border="0" /><br /><br /><br />最上段のテキストボックスがメモ入力欄になっています。<br /><br />ここにテキストを直接入力したり、開いているWebページのテキストを選択してドラッグ＆ドロップするなどし、Saveボタンをクリックすれば一つのメモとして保存されるようになっています。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/03_list_it_Firefox_Add-ons.JPG" alt="03_list_it_Firefox_Add-ons.JPG" width="255" height="473" border="0" /><br /><br /><br />保存したメモは、縦に長いリストとなって表示されるようになっており、多くのメモが隠れていても、検索窓から全文検索を行うことができるようになっています。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/04_list_it_Firefox_Add-ons.JPG" alt="04_list_it_Firefox_Add-ons.JPG" width="255" height="526" border="0" /><br /><br /><br />テキスト入力以外にも、Webページ上の画像やリンク、タブをドラッグ＆ドロップして登録することも可能です。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/05_list_it_Firefox_Add-ons.JPG" alt="05_list_it_Firefox_Add-ons.JPG" width="253" height="226" border="0" /><br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/06_list_it_Firefox_Add-ons.JPG" alt="06_list_it_Firefox_Add-ons.JPG" width="254" height="366" border="0" /><br /><br /><br />また、ステータスバーにはアイコン　<img src="http://mozilla-remix.up.seesaa.net/image/08_list_it_Firefox_Add-ons.JPG" alt="08_list_it_Firefox_Add-ons.JPG" width="20" height="24" border="0" />　が一つ追加され、これをクリックすることで、サイドバーを開いていなくても簡易メモを入力して保存することもできます。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/09_list_it_Firefox_Add-ons.JPG" alt="09_list_it_Firefox_Add-ons.JPG" width="510" height="33" border="0" /><br /><br /><br />ちょっと思いついたことなどをさっとメモするときに便利ですね。<br /><br />サイドバー上部には、オプション設定を開くボタンも用意されています。<br /><br />オプション設定では、登録時に記入したメールアドレスとパスワードを使って、list.itのサーバーと同期できるようにセットしたり、ショートカットキーの変更、list.itの背景画像の設定などができるようになっています。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/07_list_it_Firefox_Add-ons.JPG" alt="07_list_it_Firefox_Add-ons.JPG" width="370" height="479" border="0" /><br /><br /><br />同期機能を使うようにチェックを入れておけば、サイドバー上部の[Synchronize]ボタンをクリックすることでlist.itのサーバーと同期することができます。<br /><br />以上のように、機能面では非常にわかりやすくシンプルなものですが、ごちゃごちゃと操作のややこしいオンラインなどのメモツールと比べると、入力もしやすく、まさに「メモ」といった感じのツールで交換が持てます。<br /><br />思いついたことをすぐにメモするツールが欲しかった方には便利なツールになることでしょう。<br /><br /><br />ダウンロード（ユーザー登録）：<a href="http://groups.csail.mit.edu/haystack/listit/" target="_blank">list.it</a><br /><br /><br />＜関連記事＞<br /><br /><br /><a href="http://mozilla-remix.seesaa.net/article/29888917.html" target="_blank">■Firefoxにメモ帳を「QuickNote」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/38949443.html" target="_blank">■WEBサイトの気になる場所にPost-itっぽくメモを貼れる「MyStickies」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/102998648.html" target="_blank">■FirefoxでGoogle ノートブックを便利に使うためのアドオンとブックマークレット。</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/110506356.html" target="_blank">■Firefoxのブックマークにテキストノートを添付することができるアドオン「Net Notes」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/34063606.html" target="_blank">■タブでも単独でも開けるノートなアドオン「FoxNotes」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/28532594.html" target="_blank">■WEBページに付箋紙が貼れる「Internote」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/30082020.html" target="_blank">■サイドバーにNotebookを「Dappad notebook sidebar」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/106552962.html" target="_blank">■Thunderbirdにノートブック機能を追加できるアドオン「ThunderNote」</a><br /><br /><br />＜Ads＞<br /><br /><script type="text/javascript" src="http://www.accesstrade.net/at/rtt.js?rt=0009yp0025ej"></script>



<br clear="all" /><a href="http://rss.rssad.jp/rss/ad/K24wTRxnZh3Y/v_JWg0xzbmjr?type=1" target="_blank"><img src="http://rss.rssad.jp/rss/img/K24wTRxnZh3Y/v_JWg0xzbmjr?type=1" border="0"/></a><br/>]]></content:encoded>  
      <category>Firefox拡張機能（アドオン）</category>  
      <author>moziller</author> 
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
    <title>Mozilla Re-Mix</title>  
    <link>http://mozilla-remix.seesaa.net/</link>  
    <description>爆発的に普及中のFirefoxやThunderbirdはWEBユーザーの必須アイテム。初心者からヘビーユーザーまで使える！便利でクールなFirefox拡張機能（アドオン）の使い方やMozilla関連情報をどうぞ。</description>  
    <language>ja</language>  
    <docs>http://blogs.law.harvard.edu/tech/rss</docs>  
    <itunes:subtitle/>  
    <itunes:summary>爆発的に普及中のFirefoxやThunderbirdはWEBユーザーの必須アイテム。 初心者からヘビーユーザーまで使える！便利でクールなFirefox拡張機能（アドオン）の使い方やMozilla関連情報をどうぞ。</itunes:summary>  
    <itunes:keywords>Firefox Thunderbird Mozilla モジラ 拡張機能 テーマ メールソフト タブブラウザ ブラウザ アドオン Thunderbird2 バックアップ カスタマイズ ブックマーク カスタマイズ Google Chrome about:config 高速化 windows ツールバー user.js Ubiquity Firefox 3</itunes:keywords>  
    <itunes:author>ERROR: NOT PERMITED METHOD: nickname</itunes:author>  
    <itunes:owner> 
      <itunes:name/>  
      <itunes:email/> 
    </itunes:owner>  
    <itunes:explicit>no</itunes:explicit>

    <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="self" href="http://rss.rssad.jp/rss/CG5kXk4HYq9M/rss_0002" type="application/rss+xml"/>  
    <item> 
      <link>http://mozilla-remix.seesaa.net/article/111273992.html</link>  
      <title>Firefoxのサイドバーでメモを取ることができるMIT製アドオン「list.it」</title>  
      <pubDate>Mon, 15 Dec 2008 18:52:29 +0900</pubDate>  
      <description><![CDATA[<p>ローカルやWebアプリには、「メモ」を効率よく管理するようなものがたくさんあり、Firefoxでも、それらツールの専用アドオンをインストールしておくことによって、メモの管理を行うことが可能です。代表的なものに、Google ノートブックやEvernote、などがありますが、今回は、MITの研究所で作成されたというメモ管理ツール「list.it」を試してみました。</p>
]]></description>  
      <content:encoded><![CDATA[
ローカルやWebアプリには、「メモ」を効率よく管理するようなものがたくさんあり、Firefoxでも、それらツールの専用アドオンをインストールしておくことによって、メモの管理を行うことが可能です。<br /><br />代表的なものに、Google ノートブックやEvernote、などがありますが、今回は、MITの研究所で作成されたというメモ管理ツール「list.it」を試してみました。<br /><br /><br /><a name="more"></a><a href="http://groups.csail.mit.edu/haystack/listit/" target="_blank">「list.it」</a>は,firefox 3以上のサイドバーで動作するメモ帳で、テキストやリンク、画像などを気ままに追加して後で検索、参照することができるというごくシンプルなアドオンです。<br /><br />アドオンを入手するには、最初にlist.itのサイトでユーザー登録を行う必要があります。<br /><br />ページ内のget it!以下で、step 2: get it (and sign up!)をクリックし、登録用のメールアドレス、パスワードを入力し、[Register me]ボタンをクリックすれば登録確認メールが届きます。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/00_list_it_Firefox_Add-ons.JPG" alt="00_list_it_Firefox_Add-ons.JPG" width="375" height="425" border="0" /><br /><br /><br />届いたメール内下部に記載されている長いリンクをクリックすれば確認が完了し、Firefoxにアドオンのダウンロードリンクが表示されます。<br /><br />＊[Configure it!]ボタンは無視してかまいません。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/01_list_it_Firefox_Add-ons.JPG" alt="01_list_it_Firefox_Add-ons.JPG" width="510" height="441" border="0" /><br /><br /><br />アドオンをインストールして再起動すると、サイドバー表示項目に「list.it」が加わり、表示すると以下のようにかなりシンプルな画面が現れます。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/02_list_it_Firefox_Add-ons.JPG" alt="02_list_it_Firefox_Add-ons.JPG" width="255" height="293" border="0" /><br /><br /><br />最上段のテキストボックスがメモ入力欄になっています。<br /><br />ここにテキストを直接入力したり、開いているWebページのテキストを選択してドラッグ＆ドロップするなどし、Saveボタンをクリックすれば一つのメモとして保存されるようになっています。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/03_list_it_Firefox_Add-ons.JPG" alt="03_list_it_Firefox_Add-ons.JPG" width="255" height="473" border="0" /><br /><br /><br />保存したメモは、縦に長いリストとなって表示されるようになっており、多くのメモが隠れていても、検索窓から全文検索を行うことができるようになっています。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/04_list_it_Firefox_Add-ons.JPG" alt="04_list_it_Firefox_Add-ons.JPG" width="255" height="526" border="0" /><br /><br /><br />テキスト入力以外にも、Webページ上の画像やリンク、タブをドラッグ＆ドロップして登録することも可能です。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/05_list_it_Firefox_Add-ons.JPG" alt="05_list_it_Firefox_Add-ons.JPG" width="253" height="226" border="0" /><br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/06_list_it_Firefox_Add-ons.JPG" alt="06_list_it_Firefox_Add-ons.JPG" width="254" height="366" border="0" /><br /><br /><br />また、ステータスバーにはアイコン　<img src="http://mozilla-remix.up.seesaa.net/image/08_list_it_Firefox_Add-ons.JPG" alt="08_list_it_Firefox_Add-ons.JPG" width="20" height="24" border="0" />　が一つ追加され、これをクリックすることで、サイドバーを開いていなくても簡易メモを入力して保存することもできます。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/09_list_it_Firefox_Add-ons.JPG" alt="09_list_it_Firefox_Add-ons.JPG" width="510" height="33" border="0" /><br /><br /><br />ちょっと思いついたことなどをさっとメモするときに便利ですね。<br /><br />サイドバー上部には、オプション設定を開くボタンも用意されています。<br /><br />オプション設定では、登録時に記入したメールアドレスとパスワードを使って、list.itのサーバーと同期できるようにセットしたり、ショートカットキーの変更、list.itの背景画像の設定などができるようになっています。<br /><br /><br /><img src="http://mozilla-remix.up.seesaa.net/image/07_list_it_Firefox_Add-ons.JPG" alt="07_list_it_Firefox_Add-ons.JPG" width="370" height="479" border="0" /><br /><br /><br />同期機能を使うようにチェックを入れておけば、サイドバー上部の[Synchronize]ボタンをクリックすることでlist.itのサーバーと同期することができます。<br /><br />以上のように、機能面では非常にわかりやすくシンプルなものですが、ごちゃごちゃと操作のややこしいオンラインなどのメモツールと比べると、入力もしやすく、まさに「メモ」といった感じのツールで交換が持てます。<br /><br />思いついたことをすぐにメモするツールが欲しかった方には便利なツールになることでしょう。<br /><br /><br />ダウンロード（ユーザー登録）：<a href="http://groups.csail.mit.edu/haystack/listit/" target="_blank">list.it</a><br /><br /><br />＜関連記事＞<br /><br /><br /><a href="http://mozilla-remix.seesaa.net/article/29888917.html" target="_blank">■Firefoxにメモ帳を「QuickNote」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/38949443.html" target="_blank">■WEBサイトの気になる場所にPost-itっぽくメモを貼れる「MyStickies」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/102998648.html" target="_blank">■FirefoxでGoogle ノートブックを便利に使うためのアドオンとブックマークレット。</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/110506356.html" target="_blank">■Firefoxのブックマークにテキストノートを添付することができるアドオン「Net Notes」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/34063606.html" target="_blank">■タブでも単独でも開けるノートなアドオン「FoxNotes」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/28532594.html" target="_blank">■WEBページに付箋紙が貼れる「Internote」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/30082020.html" target="_blank">■サイドバーにNotebookを「Dappad notebook sidebar」</a><br /><br /><a href="http://mozilla-remix.seesaa.net/article/106552962.html" target="_blank">■Thunderbirdにノートブック機能を追加できるアドオン「ThunderNote」</a><br /><br /><br />＜Ads＞<br /><br /><script type="text/javascript" src="http://www.accesstrade.net/at/rtt.js?rt=0009yp0025ej"></script>



<br clear="all" /><br/>]]></content:encoded>  
      <category>Firefox拡張機能（アドオン）</category>  
      <author>moziller</author> 
    </item>
  </channel> 
</rss>
__TEST__
))
    	);
    }
}
?>
