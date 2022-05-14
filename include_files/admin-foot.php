<?php
if(class_exists('DiagnosisAdmin')){
	global $osdg_option_data;
	$license = 'free';
	//
	if(isset($osdg_option_data) && isset($osdg_option_data['license'])){
		$license = DiagnosisResultClass::licenseCheck($osdg_option_data['license']);
	}
	//
	if($license=='free'){
?>
			<ul class="footer-pr">
				<li><a href="http://lp.olivesystem.jp/wordpress%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3%E9%96%8B%E7%99%BA" target="_blank">WordPressプラグイン開発します</a>
					<ul>
						<ol>様々な用途のプラグインを開発しております。既存プラグインのカスタマイズも可能です。</ol>
					</ul>
				</li>
				<li><a href="http://lp.olivesystem.jp/wordpress%e3%81%ae%e5%9b%b0%e3%82%8a%e4%ba%8b%e3%80%81%e7%9b%b8%e8%ab%87" target="_blank">WordPressのご相談</a></li>
					<ul>
						<ol>WordPressサイトのカスタマイズ、バグやエラーの修正など。まずはお気軽にご相談ください。</ol>
					</ul>
			</ul>
<?php
	}
?>
			<div class="footer-copyright"> &copy; 2014-<?php echo date_i18n("Y"); ?> 診断ジェネレータ作成プラグイン : OLIVESYSTEM （オリーブシステム）</div>
<?php
}
?>