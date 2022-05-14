<?php
if(class_exists('DiagnosisAdmin')){
?>
	<div id="diagnosis-plugin">
	<?php include_once(OSDG_PLUGIN_INCLUDE_FILES."/admin-head.php"); ?>
		<div class="diagnosis-wrap">
			<a name="license" id="license"></a>
			<h2>ライセンスについて</h2>
			<div class="diagnosis-contents">
				当プラグインは基本無料ですが、著作権表示等は削除しないでください。<br />
				診断ページに表示されるプラグイン名によるリンクを非表示にしたい場合は、ライセンスを取得してください。<br />
				<p><a href="http://lp.olivesystem.jp/cart/plugindglicense" target="_blank">ライセンス取得はこちら</a></p>
				1サイトにつき有効になりますので、複数のサイトでご利用になられる場合はその都度ご連絡ください。
			</div>
			<a name="pro" id="pro"></a>
			<h2>PRO版について</h2>
			<div class="diagnosis-contents">
				<p>通常版にはない機能をつけたPRO版をリリースしました。</p>
				<strong>主な追加機能</strong>
				<ul style="padding-left:25px;">
					<li style="list-style:circle;">ユーザの診断結果をデータベース化</li>
					<li style="list-style:circle;">管理画面にて、ユーザの検索、ユーザ詳細表示</li>
					<li style="list-style:circle;">上記のデータをCSV出力</li>
				</ul>
				<p>詳しくは、<a href="http://lp.olivesystem.jp/cart/plugindgpro" target="_blank">こちら</a>をご覧ください。</p>
			</div>
		</div>
		<?php include_once(OSDG_PLUGIN_INCLUDE_FILES."/admin-foot.php"); ?>
	</div>

<?php
}
?>