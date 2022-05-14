<?php
if(class_exists('DiagnosisAdmin')){
?>

	<div id="diagnosis-plugin">
	<?php include_once(OSDG_PLUGIN_INCLUDE_FILES."/admin-head.php"); ?>
		<div class="diagnosis-wrap">
			<h2>診断フォームテーマ作成、編集</h2>
<?php
if(!DiagnosisSqlClass::show_table(OSDG_PLUGIN_FORM_OPTIONS)){
	print "<div style=\"color:red;font-size:22px;font-weight:bold;\">まだテーマ用のテーブルが作成されておりません。<br>プラグインを一度無効化し、再び有効化してみてください。</div>\n";
}
?>
			<div class="diagnosis-contents diagnosis-theme-write">
				<div style="margin-bottom:25px;">
					<p>診断フォームに使用できるテーマ（CSSファイル、JavaScriptファイル）を作成・編集できます。</p>
					<p>
						<input type="radio" name="first_select" id="first-select0" value="0" class="first-select" <?php if(empty($view_type) || $view_type=='select0'){ ?>checked<?php } ?> /><label for="first-select0" class="first-select-label">テーマを編集する</label>
						&nbsp;
						<input type="radio" name="first_select" id="first-select1" value="1" class="first-select" <?php if($view_type=='select1'){ ?>checked<?php } ?> /><label for="first-select1" class="first-select-label">テーマを作成する</label>
						<p><small>選択すると表示が切り替わります</small></p>
					</p>
				</div>
				<a id="form-heading" name="form-heading"></a>
				<div id="div-write">
					<p>既存テーマを編集します。</p>
					<div class="msg"><?php echo $message; ?></div>
					<div>
						<select id="writetheme" name="writetheme">
<?php
						foreach($dir_list as $dir){
							if(!isset($first_dir)){
								$first_dir = $dir;
							}
?>
							<option value="<?php echo esc_html($dir); ?>"><?php echo esc_html($dir); ?></option>
<?php
						}
?>
						</select>
						<span class="submit"><input type="button" id="theme-write" value="編集する" style="width:80px;" /></span>
					</div>
					<form action="admin.php?page=diagnosis-generator-theme.php" id="theme-form" method="POST">
						<div class="clearfix">
							<div class="diagnosis-input">
								<div id="block-css" class="left">
									<p>style.css</p>
									<textarea id="css-textarea" name="css"></textarea>
								</div>
								<div id="block-js" class="left">
									<p>theme.js</p>
									<textarea id="js-textarea" name="js"></textarea>
								</div>
							</div>
						</div>
						<input type="hidden" name="theme_name" id="theme-name" value="<?php echo $first_dir; ?>" />
						<?php wp_nonce_field($my_id.'_writetheme', '_wpnonce', false); echo "\n"; ?>
						<p class="submit"><input type="button" id="theme-update" value="更新する" style="width:80px;" /></p>
					</form>
				</div>
				<a id="add-heading" name="add-heading"></a>
				<div id="div-add">
					<p>テーマを新規作成します。</p>
					<div class="add-msg"><?php echo $message2; ?></div>
					<form action="admin.php?page=diagnosis-generator-theme.php" id="theme-add" method="POST">
						<p><label for="add-name">テーマ名</label><input type="text" name="add_name" id="add-name" value="" placeholder="半角英数字のテーマ名" /></p>
						<p>
							<input type="radio" name="add_type" id="add-type1" value="copy" checked /><label for="add-type1">既存のテーマをコピーして作成</label>
							&nbsp;&nbsp;
							<input type="radio" name="add_type" id="add-type0" value="new" /><label for="add-type0">新たなテーマで作成</label>
						</p>
						<div class="copy-select">
							<select id="copy-theme" name="copy_theme">
								<option value="">選択してください</option>
<?php
						foreach($dir_list as $dir){
							if(!isset($first_dir)){
								$first_dir = $dir;
							}
?>
								<option value="<?php echo esc_html($dir); ?>"><?php echo esc_html($dir); ?></option>
<?php
						}
?>
							</select>をコピーする
						</div>
						<?php wp_nonce_field($my_id.'_addtheme', '_wpnonce', false); echo "\n"; ?>
						<p class="submit"><input type="button" id="theme-add-button" value="作成する" style="width:80px;" /></p>
					</form>
				</div>
			</div>
		</div>
		<?php include_once(OSDG_PLUGIN_INCLUDE_FILES."/admin-foot.php"); ?>
	</div>

	<script>
	// 編集表示か作成表示か
	function view_edit(str){
		if(str==1){
			jQuery('#div-write').hide();
			jQuery('#div-add').show();
		}
		else{
			jQuery('#div-write').show();
			jQuery('#div-add').hide();
		}
	}
	// テーマのcss出力
	function print_css(theme_name){
		jQuery('#css-textarea').val('…読込中…');
		//
		jQuery.ajax({
			type: "GET",
			url: "admin.php",
			data: "page=diagnosis-generator-theme.php&type=css&theme="+theme_name,
			success: function(result){
				jQuery('#css-textarea').val(result);
			}
		});
	}
	// テーマのjs出力
	function print_js(theme_name){
		jQuery('#js-textarea').val('…読込中…');
		//
		jQuery.ajax({
			type: "GET",
			url: "admin.php",
			data: "page=diagnosis-generator-theme.php&type=js&theme="+theme_name,
			success: function(result){
				jQuery('#js-textarea').val(result);
			}
		});
	}
	// 新規作成、コピーかどうかの表示
	function view_copytheme(){
		var add_type = jQuery('input[name="add_type"]:checked').val();
		if(add_type=='copy'){
			jQuery('.copy-select').show();
		}
		else{
			jQuery('.copy-select').hide();
		}
	}
	// 読込時
	jQuery(document).ready(function(){
		var theme_name = jQuery('#theme-name').val();
		print_css(theme_name);
		print_js(theme_name);
		//
		var first_select = jQuery('input[name="first_select"]:checked').val();
		view_edit(first_select);
		//
		view_copytheme();
	});
	// 表示変更
	jQuery(document).on('click', '.first-select', function(){
		var value = jQuery(this).val();
		view_edit(value);
	});
	// テーマ編集、選択変更時
	jQuery(document).on('change', '#writetheme', function(){
		var theme_name = jQuery(this).val();
		jQuery('#theme-name').val(theme_name);
	});
	// テーマ編集、編集する
	jQuery(document).on('click', '#theme-write', function(){
		var theme_name = jQuery('#theme-name').val();
		print_css(theme_name);
		print_js(theme_name);
	});
	// テーマ編集、更新する
	jQuery(document).on('click', '#theme-update', function(){
		var $form = jQuery('#theme-form');
		//
		jQuery.ajax({
			url: $form.attr('action'),
			type: $form.attr('method'),
			data: $form.serialize(),
			// 通信成功時の処理
			success: function(result, textStatus, xhr) {
				if(result.match(/[1-9]/gi)){
					jQuery('.msg').html('<p>更新しました</p>');
				}
				else{
					jQuery('.msg').html('<p>更新エラー！</p>');
				}
			},
			// 通信失敗時の処理
			error: function(xhr, textStatus, error) {
				jQuery('.msg').html('<p>通信エラー！</p>');
			}
		});
		// 移動
		jQuery("html,body").animate({scrollTop:jQuery('#form-heading').offset().top});
	});
	// 新規作成、コピーかどうかの表示
	jQuery(document).on('click', 'input[name="add_type"]', function(){
		view_copytheme();
	});
	// 作成するがクリックされたときの動作
	jQuery(document).on('click', '#theme-add-button', function(){
		var msg = '';
		var theme_name = jQuery('#add-name').val();
		// 入力チェック
		if(theme_name==''){
			msg += '<p>テーマ名を入力してください。</p>';
		}
		else{
			// 半角数字、アンダーバー、ハイフン以外はNG
			if(!theme_name.match(/^([0-9a-zA-Z_-]+)$/i)){
				msg += '<p>禁止された文字です。テーマ名は、半角英数字、アンダーバー、ハイフンが使用できます。</p>';
			}
			// 既に同じテーマ名がないかチェック
			var obj = jQuery('#copy-theme').children();
			for(var i=0; i<obj.length; i++){
				if(obj.eq(i).val()==theme_name){
					msg += '<p>同名のテーマが存在します。</p>';
					break;
				}
			}
		}
		// コピーなら
		var add_type = jQuery('input[name="add_type"]:checked').val();
		if(add_type=='copy'){
			if(jQuery('#copy-theme').val()!==""){

			}
			else{
				msg += '<p>コピーするテーマを選択してください。</p>';
			}
		}
		// エラーメッセージがなければ実行
		if(msg===""){
			var $form = jQuery('#theme-add');
			jQuery.ajax({
				url: $form.attr('action'),
				type: $form.attr('method'),
				data: $form.serialize(),
				// 通信成功時の処理
				success: function(result, textStatus, xhr) {
					if(result.match(/[1-9]/gi)){
						var url = 'admin.php?page=diagnosis-generator-theme.php&view_type=select1&msg2=insert-ok';
						location.href = url; // 成功ならリダイレクトする
					}
					else{
						jQuery('#div-add .add-msg').html('<p>作成エラー！</p>');
					}
				},
				// 通信失敗時の処理
				error: function(xhr, textStatus, error) {
					jQuery('#div-add .add-msg').html('<p>通信エラー！</p>');
				}
			});
		}
		else{
			jQuery('#div-add .add-msg').html(msg);
		}
		// スクロール
		jQuery('html,body').animate({scrollTop:jQuery('#add-heading').offset().top});
	});
	</script>

<?php
}
?>