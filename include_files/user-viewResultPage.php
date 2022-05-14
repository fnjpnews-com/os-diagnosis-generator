<?php
if(class_exists('DiagnosisView')){
	if(empty($data)){
		$message .= 'データがありません！';
	}
	//
	$class = new DiagnosisView();
	$user_contents = '';
	//
	if(isset($error_jscript)){
		$user_contents .= $error_jscript;
	}
	//
	$theme_script = '';

$user_page_view=<<<_EOD_
	<div id="diagnosis-plugin">
		<div class="diagnosis-wrap">
			<div class="red_message">{$message}</div>
_EOD_
;
$user_contents .= $user_page_view."\n";

	if(!empty($data)){

			// テーマ
			$theme = (isset($data['diagnosis_theme'])) ? $data['diagnosis_theme']: '';
			$theme_script = '';
			// あればテーマファイル呼び出し
			if(!empty($theme)){
				$plugin_url = plugins_url('os-diagnosis-generator');
				$theme_script = "\t".'<link type="text/css" rel="stylesheet" href="'.$plugin_url.'/theme/'.esc_html($theme).'/style.css"/>'."\n";
				$theme_script .= "\t".'<script type="text/javascript" src="'.$plugin_url.'/theme/'.esc_html($theme).'/theme.js" ></script>'."\n";
			}

			// フォームタイトル
			if(!empty($data['form_title_flag'])){
				$form_title = '<h3 class="diagnosis-form-title">'.self::h($data['form_title']).'</h3>';
			}else{
				$form_title = '';
			}
			// フォームヘッダ
			$form_header = '';
			if(!empty($data['display_flag'])){
				if(empty($data['after_header_flag'])){
					if(!empty($data['form_header'])){
						$form_header = self::h_dec($data['form_header']);
					}
				}else{
					if(!empty($data['form_after_header'])){
						$form_header = self::h_dec($data['form_after_header']);
					}
				}
			}

$user_page_view=<<<_EOD_
			<div id="diagnosis-form" class="diagnosis-result">
				<div class="diagnosis-form-header">
					{$form_header}
				</div>
				{$form_title}
_EOD_
;
$user_contents .= $user_page_view."\n";

$user_contents .= nl2br($result);

			// フォームフッタ
			$form_footer = '';
			if(!empty($data['display_flag'])){
				if(empty($data['after_footer_flag'])){
					if(!empty($data['form_footer'])){
						$form_footer = self::h_dec($data['form_footer']);
					}
				}else{
					if(!empty($data['form_after_footer'])){
						$form_footer = self::h_dec($data['form_after_footer']);
					}
				}
			}
			//
			if(!empty($data['form_etc'])){
				$form_etc = $data['form_etc'];
			}

$user_page_view=<<<_EOD_
			</div>
			<div class="diagnosis-form-footer">
				{$form_footer}{$form_etc}
			</div>
_EOD_
;
$user_contents .= $user_page_view."\n";

	}
$user_contents .= self::osdgLicense(1);
$user_page_view=<<<_EOD_
		</div>
	</div>
{$theme_script}
_EOD_
;
$user_contents .= $user_page_view."\n";
$content = $user_contents;
}
?>