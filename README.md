Conveyor(Prhagger) for rhaco1
===================

##インストール
		rhacoのダウンロード: http://github.com/tokushima/rhaco1/releases/tag/1.6.2
		解凍後、rhacoフォルダをConveyorのlibrary以下へ配置します。

##初期処理
		ブラウザからsetup.phpを実行するとセットアップ画面が表示します
		[settings] settingボタンを押下します。


##Lineの作成
		ブラウザからConveyor(index.php)を実行するとLine生成のUIが表示されます。
		LineフォームのNameにLineの名前を入力します。(今回はfeed)
		右のWorkersリストからFeedInを左のWorkerフォームへドラッグします。
		右のWorkersリストからHtmlOutを左のWorkerフォームへドラッグします。
		追加したWorkerフォームFeedInのConfigs内「RSSのURL」にフィードを含むURLを入力します。
		Generateボタンを押下します。

###フィードを含むURLの例
		http://japan.cnet.com
		http://b.hatena.ne.jp/hotentry

##Lineの実行
		setup.phpの[settings]アプリケーションデータの設定 > 実行ファイルの出力場所 で指定されたパスに
		LineフォームのNameで指定した名前 + .phpのファイル名でLineファイルが出力されています。

		> ~/publish/feed.php
