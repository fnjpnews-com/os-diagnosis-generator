<?php
if(class_exists('DiagnosisAdmin')){
	global $pro_flag;
?>

		<div id="diagnosis-header" class="clearfix">
			<div class="plugin-name">診断ジェネレータ作成プラグイン<?php if(!empty($pro_flag)){ echo "PRO"; } ?> <?php echo OSDG_PLUGIN_VERSION; ?></div>
			<div class="header-link"><a href="http://lp.olivesystem.jp/category/wordpress-plugins" target="_blank">お知らせ・プラグイン情報</a> | <a href="http://lp.olivesystem.jp/wordpress" target="_blank">サイト制作</a> | <a href="http://lp.olivesystem.jp/wordpress%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3%E9%96%8B%E7%99%BA" target="_blank">プラグイン開発</a></div>
		</div>
		<ul class="plugin-list">
			<li class="first"><a href="admin.php?page=diagnosis-generator-admin.php">はじめに</a></li>
			<li><a href="admin.php?page=diagnosis-generator-new.php">新規作成</a></li>
			<li><a href="admin.php?page=diagnosis-generator-list.php">診断フォーム一覧</a></li>
			<li><a href="admin.php?page=diagnosis-generator-theme.php">テーマ編集</a></li>
			<li><a href="admin.php?page=diagnosis-generator-options.php">オプション</a></li>
<?php
	if(empty($pro_flag)){ ?>
			<li><a href="admin.php?page=diagnosis-generator-admin.php&amp;mode=license#license">ライセンス取得</a></li>
			<li><a href="admin.php?page=diagnosis-generator-admin.php&amp;mode=license#pro">プラグインPRO版</a></li>
<?php
	} ?>
		</ul>

<?php
}
?>