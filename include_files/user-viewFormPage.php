<?php
if(class_exists('DiagnosisView')){
	if(empty($data_arr)){
		$message .= 'データがありません！';
	}
	//
	$class = new DiagnosisView();
	$user_contents = '';
	// POSTの場合
	if(!empty($post) && !empty($post['diagnosis_id'])){
		$check_pid = $post['diagnosis_id'];
	}
	//
	$theme_script = '';

$user_page_view=<<<_EOD_
<script>
function click_views(ids){
	jQuery('#'+ids).css("display", "block");
}
function click_views_none(ids){
	jQuery('#'+ids).css("display", "none");
}
</script>
_EOD_
;
$user_contents .= $user_page_view."\n";

	if(isset($error_jscript)){
		$user_contents .= $error_jscript;
	}

$user_page_view=<<<_EOD_
	<div id="diagnosis-plugin">
		<div class="diagnosis-wrap">
_EOD_
;
$user_contents .= $user_page_view."\n";
	// POSTじゃなければヘッダ側にエラーテキスト
	if(!isset($check_pid)){
		$user_contents .= "\t\t\t<div class=\"red_message\">{$message}</div>\n";
	}
	// データがあれば
	if(!empty($data_arr)){

		foreach($data_arr as $data){
			// submitボタンの値
			if(!empty($data['form_submit_text'])){
				$sbm_text = self::h($data['form_submit_text']);
			}else{
				$sbm_text = '診断する';
			}
			// プレースホルダ
			if(!empty($data['form_input_placeholder'])){
				$placeholder = self::h($data['form_input_placeholder']);
			}else{
				$placeholder = '';
			}
			// フォームタイトル
			if(!empty($data['form_title_flag'])){
				$form_title = '<h3 class="diagnosis-form-title">'.self::h($data['form_title']).'</h3>';
			}else{
				$form_title = '';
			}
			// フォームヘッダ
			if(!empty($data['display_flag']) && !empty($data['form_header'])){
				$form_header = self::h_dec($data['form_header']);
			}else{
				$form_header = '';
			}
			// フォームclass
			if(!empty($data['display_flag']) && !empty($data['form_class'])){
				$css_class = self::h($data['form_class']);
			}else{
				$css_class = '';
			}
			//
			if(isset($check_pid) && $check_pid==$data['data_id']){ // 該当idなら
				$error_message = "<div class=\"red_message\">{$message}</div>\n";
			}else{
				$error_message = '';
			}
			// バグ回避
			if(!empty($osdg_option_data['not404'])){
				$form_action = '';
			}else{
				$form_action = '#osdg-form'.$data['data_id'];
			}
			// テーマ
			$theme = (isset($data['diagnosis_theme'])) ? $data['diagnosis_theme']: '';
			// あればテーマファイル呼び出し
			if(!empty($theme)){
				$plugin_url = plugins_url('os-diagnosis-generator');
				$theme_script = "\t".'<link type="text/css" rel="stylesheet" href="'.$plugin_url.'/theme/'.esc_html($theme).'/style.css"/>'."\n";
				$theme_script .= "\t".'<script type="text/javascript" src="'.$plugin_url.'/theme/'.esc_html($theme).'/theme.js" ></script>'."\n";
			}
			// nonceのキャッシュ対策
			$nonce_cache_sol = (!empty($data['nonce_cache_sol'])) ? $data['nonce_cache_sol']: 0;

$user_page_view=<<<_EOD_
			<div id="osdg-form{$data['data_id']}" class="diagnosis-form">
				<div class="diagnosis-form-header">
					{$form_header}
				</div>
				{$form_title}
				{$error_message}
				<form action="{$form_action}" method="POST" class="{$css_class}">
					<div class="form-message"></div>
_EOD_
;
$user_contents .= $user_page_view."\n";

			if(!empty($data['form_input_label'])){

$user_page_view=<<<_EOD_
					<span class="label">
						<div style="text-align: center"><label for="diagnosis-name">お名前を入力してください</label></div>
					</span>
_EOD_
;
$user_contents .= $user_page_view."\n";

			}

$user_page_view=<<<_EOD_
					<span class="cols">
						<div style="text-align: center"><input type="text" name="diagnosis_name" id="diagnosis-name" placeholder="{$placeholder}" value="{$class->post_set('diagnosis_name', '1')}" /></div>
					</span>
_EOD_
;
$user_contents .= $user_page_view."\n";

				if(isset($data['diagnosis_type']) && $data['diagnosis_type']==0){
				// システム任せのフォーム //////////////////////////////////

$user_page_view=<<<_EOD_
                    <span class="image">
                        <div style="text-align: center"><input type="image" src='https://fnjpnews.com/wp-content/uploads/2022/04/result.png' style="height: 80px;" value="{$sbm_text}" /></div>
                    </span>
_EOD_
;
$user_contents .= $user_page_view."\n";

				}elseif(isset($data['diagnosis_type']) && $data['diagnosis_type']==1){
				// 設問形式のフォーム //////////////////////////////////
					if(!empty($data['diagnosis_count'])){
						$diagnosis_count = $data['diagnosis_count'];
					}else{
						$diagnosis_count = 10;
					}
					//
					if(!empty($data['question'])){
						$q = 1;

						// 設問表示 start
						foreach($data['question'] as $key => $question){
							$sid = $question['sort_id'];
							$line = explode("\n", trim($question['choice']));
							$l = 1;
							if(empty($question['text'])){
								break;
							}
							$checked = self::post_set('question.'.$sid, 1);

$user_page_view=<<<_EOD_
					<div id="block-question{$q}" class="question">
						<div class="qcontents"><span class="question-number">問{$q}</span><span class="question-delimiter"> : </span><span class="question-text"><div style="text-align: center">{$question['text']}</span></div>
						<div class="qselect" id="question{$q}">
							<input type="radio" name="question[{$sid}]" value="0" style="display:none;" {$class->if_empty_checked($checked)} /></div>
_EOD_
;
$user_contents .= $user_page_view."\n";

							foreach($line as $ln){
								$ln = trim($ln);
$user_page_view=<<<_EOD_
	<span class="choose"><input type="radio" name="question[{$sid}]" id="inp-question{$sid}-c{$l}" value="{$l}" {$class->if_ecall_checked($checked, $l)} /><label for="inp-question{$sid}-c{$l}">{$ln}</label></span>
_EOD_
;
$user_contents .= $user_page_view."\n";

								$l++;
							}
$user_page_view=<<<_EOD_
						</div>
					</div>
_EOD_
;
$user_contents .= $user_page_view."\n";
							//
							if($q==$diagnosis_count){
								break;
							}
							$q++;
						}
						// 設問表示 end
						// 設問data_id start
						if(isset($data['question'][0]) && isset($data['question'][0]['data_id'])){

$user_page_view=<<<_EOD_
					<input type="hidden" name="data_id" value="{$data['question'][0]['data_id']}" />
_EOD_
;
$user_contents .= $user_page_view."\n";

						} // 設問data_id end

$user_page_view=<<<_EOD_
					<br />
					<div class="image">
                        <input type="button" id="back-button" class="nb-button" value="&lt;&nbsp;戻る" style="display:none;" />
                        <input type="button" id="next-button" class="nb-button" value="次へ&nbsp;&gt;" style="display:none;" />
						<input type="image" src='https://fnjpnews.com/wp-content/uploads/2022/04/result.png' style="height: 80px;" value="{$sbm_text}" />
					</div>
_EOD_
;
$user_contents .= $user_page_view."\n";

					}else{

$user_page_view=<<<_EOD_
					<div class="red_message">設問がありません</div>
_EOD_
;
$user_contents .= $user_page_view."\n";

					}

				}

			// フォームフッタ
			if(!empty($data['display_flag']) && !empty($data['form_footer'])){
				$form_footer = self::h_dec($data['form_footer']);
			}else{
				$form_footer = '';
			}
			//
			if(!empty($data['form_etc'])){
				$form_etc = $data['form_etc'];
			} else {
				$form_etc = '';
			}

			if(!empty($nonce_no) && $nonce_no=='no'){

			}else{
				$nonce_field = wp_nonce_field(OSDG_NONCE_ACTION, OSDG_NONCE_NAME, true, false);
			}

$user_page_view=<<<_EOD_
					{$nonce_field}
					<input type="hidden" name="uniq" value="{$uniqid}" />
					<input type="hidden" name="diagnosis_id" value="{$data['data_id']}" />
					<input type="hidden" name="diagnosis_plugin" value="1" />
				</form>
				<div class="diagnosis-form-footer">
					{$form_footer}{$form_etc}
				</div>
			</div>
_EOD_
;
$user_contents .= $user_page_view."\n";

		}
	}
	$user_contents .= self::osdgLicense(1);
	//
	if(!empty($nonce_cache_sol)){
		ob_start();
?>
<form action="<?php echo site_url(); ?>" id="nonce-update" method="POST">
    <input type="hidden" name="mode" value="osdg-again-nonce" />
</form>
<script>
// nonceキャッシュ対策
jQuery(document).ready(function() {
    var $form = jQuery('#nonce-update');
    //
    jQuery.ajax({
        url: $form.attr('action'),
        type: $form.attr('method'),
        data: $form.serialize(),
        // 通信成功時の処理
        success: function(result, textStatus, xhr) {
            ary = result.split(',');
            // 値を変更
            jQuery('[name="<?php echo OSDG_NONCE_NAME; ?>"]').val(ary[0]);
        },
        // 通信失敗時の処理
        error: function(xhr, textStatus, error) {}
    });
});
</script>
<?php
		$theme_script .= ob_get_contents();
		ob_end_clean();
	}
//
$plg_version = defined('OSDG_PLUGIN_VERSION') ? OSDG_PLUGIN_VERSION: '0.0.0';
$plg_pro_version = defined('OSDGPRO_PLUGIN_VERSION') ? ' : pv'.OSDGPRO_PLUGIN_VERSION: '';
$update_time = isset($data['update_time']) ? $data['update_time']: '';
$user_page_view=<<<_EOD_
			<input type="hidden" id="diagnosis-point" class="osdg-point" value="0" />
		</div>
	</div>
	<!-- OSDG FORM -->
	<!-- v{$plg_version}{$plg_pro_version} : {$update_time} -->
{$theme_script}
_EOD_
;
$user_contents .= $user_page_view."\n";
$content = $user_contents;
}
?>