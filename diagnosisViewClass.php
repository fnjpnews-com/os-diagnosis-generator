<?php
class PreDiagnosisView extends DiagnosisClass {

	public function __construct(){

		parent::__construct();
		//
		add_action('init', array('DiagnosisView', 'init_diagnosis_mode'));
		// 表示のショートコード
		add_shortcode('OSDGSIS-FORM', array('DiagnosisView', 'viewMode'));
		add_shortcode('OSDGSIS-RESULT-FORM', array('DiagnosisView', 'viewResultMode')); // 診断結果のショートコード
		// 大文字だと表示できないこともあるので小文字も用意
		add_shortcode('osdgsis-form', array('DiagnosisView', 'viewMode'));
		add_shortcode('osdgsis-result-form', array('DiagnosisView', 'viewResultMode')); // 診断結果のショートコード
		// 念のためハイフンなしも用意
		add_shortcode('formosdgsis', array('DiagnosisView', 'viewMode'));
		add_shortcode('formosdgsisresult', array('DiagnosisView', 'viewResultMode')); // 診断結果のショートコード
		// JS、CSS読み込み
		add_action('wp_print_scripts', array('DiagnosisView', 'os_wp_enqueue'));
		// nonceフィールド用
		add_action('init', array('DiagnosisView', 'again_nonce_mode'));

	}
	/*
	*  ショートコードの処理
	*/
	public static function viewMode($atts, $content=null){

		extract(shortcode_atts(array(
			'id' => '', 'nonce'=>'',
		), $atts));
		// 処理用に配列にする
		$arr = array(
			'id'=>$id, 'nonce'=>$nonce,
		);
		//
		if(empty($_GET['osdgl'])){
			$content = do_shortcode(DiagnosisView::shortcode_view($content, $arr));
		}else{
			if(!empty($_GET['osdgid'])){ // idが指定されていれば
				$arr['id'] = self::h($_GET['osdgid']);
			}
			$content = do_shortcode(DiagnosisView::shortcode_result_view($content, $arr));
		}

		return $content;

	}
	// 診断結果(個別の場合に使用)
	public static function viewResultMode($atts, $content=null){

		$GLOBALS['osdgsis_plugin'] = 1;

		extract(shortcode_atts(array(
			'id' => '',
		), $atts));
		// 処理用に配列にする
		$arr = array(
			'id'=>$id,
		);
		if(!empty($_GET['osdgid'])){ // idが指定されていれば
			$arr['id'] = self::h($_GET['osdgid']);
		}
		$content = do_shortcode(self::shortcode_result_view($content, $arr));

		return $content;

	}
	/*
	*  診断の表示
	*/
	// 診断フォームの表示
	public static function shortcode_view($content='', $arr=array()){

		$message = '';

		if(!empty($arr['id'])){
			global $osdg_option_data;
			$uniqid = uniqid();
			$iplong = ip2long($_SERVER["REMOTE_ADDR"]);
			// データ
			$data_arr = DiagnosisSqlClass::get_diagnosis_plurality($arr['id']);
			$nonce_no = (isset($arr['nonce']) && $arr['nonce']=='no') ? $arr['nonce']: '';
			// POST時
			if(!empty($_POST['diagnosis_plugin'])){
				$nonce_action = OSDG_NONCE_ACTION;
				$nonce_name = OSDG_NONCE_NAME;
				// nonceの認証成功
				if((!empty($_POST[$nonce_name]) && wp_verify_nonce($_POST[$nonce_name], $nonce_action)) || !empty($nonce_no)){
					$post = self::post_escape();
					// データid
					if(!empty($post['diagnosis_id'])){
						$data_id = $post['diagnosis_id'];
					}elseif(!empty($arr['id'])){
						$data_id = $arr['id'];
					}
					// 投稿権限チェック
					$post_authority = self::post_authority_check($data_id, $data_arr);
					// 権限OKなら
					if($post_authority==0){
						$validate = self::validation_post($post); // バリデーション
						// 通過
						if($result = DiagnosisValidationClass::validates($validate)){ // チェックOK
							$check_data = DiagnosisSqlClass::get_diagnosis($data_id); // データ取得
							$return_data = self::diagnosis_post($check_data, $post); // データを元に解析
							$url_name = urlencode($post['diagnosis_name']);
							// 成功なら
							if(!empty($return_data['line']) || !empty($return_data['img'])){
								//
								$return_point = (isset($return_data['point'])) ? $return_data['point'] : '';
								$url_array = array('osdgl'=>$return_data['line'], 'osdgimg'=>$return_data['img'], 'osdgid'=>$data_id, 'osdgn'=>$url_name, 'osdgpp'=>$return_point);
								// 結果表示が別ページに設定されていれば
								if(!empty($check_data['display_flag']) && !empty($check_data['result_page']) && !empty($check_data['result_page_url'])){
									// メールで結果を飛ばす場合
									if($check_data['result_page']==2){
										$url = DiagnosisResultMailClass::send_result_mail($data_id, $url_array);
										// メールのときだけ、この時点でリダイレクト
										self::os_redirect($url);
									}else{
										$jump_url = trim($check_data['result_page_url']);
									}
								}else{
									if (!function_exists('is_plugin_active')) {
										include( ABSPATH . 'wp-admin/includes/plugin.php' );
									}
									if (is_plugin_active('osdg-shortcode-custom')) {
										$jump_url = '?osdg_custom=1&form_id='.$data_id;
									} else {
										$jump_url = '';
									}
								}
								//
								$url = self::url_plus($url_array, $jump_url);
							}else{
								$url = self::url_plus(array('msg'=>'dg-error'));
							}
							// リダイレクト
							self::os_redirect($url);
						}else{ // チェックNG
							$error_jscript = DiagnosisValidationClass::js_error_css($validate);
							$message .= DiagnosisValidationClass::pmessage($validate);
						}
					}else{ // 投稿権限NG
						$message .= "このフォームは登録ユーザのみ使用できます。";
					}
				}elseif(empty($_POST[$nonce_name])){ // nonce取得できず
					$message .= "nonceが取得できません。";
				}else{ // nonce認証失敗
					$message .= "POST認証に失敗しました。";
				}
			}

			$message .= DiagnosisMessageClass::updateMessage('1');
			include(OSDG_PLUGIN_INCLUDE_FILES."/user-viewFormPage.php");

		}

		return $content;

	}
	// 投稿権限チェック
	public static function post_authority_check($data_id='', $data=''){

		global $osdg_option_data; // オプションデータ
		global $osdg_user_data; // ユーザデータ
		$user_level = $osdg_user_data['level'];
		$refusal = 1; // 1で拒否
		// 設定があれば実行
		if(isset($osdg_option_data['post_authority'])){
			switch($osdg_option_data['post_authority']){
				case '2': // 登録ユーザのみが実行できる
					if($user_level!='guest'){ // ゲストじゃなければ
						$refusal = 0;
					}
					break;
				case '3': // フォームごとに設定
					foreach($data as $d){
						if($d['data_id']==$data_id){
							switch($d['post_authority']){
								case '2':
									if($user_level!='guest'){ // ゲストじゃなければ
										$refusal = 0;
									}
									break;
								default:
									$refusal = 0;
							}
							break;
						}
					}
					break;
				default:
					$refusal = 0;
			}
		}else{ // なければフォーム実行可能にする
			$refusal = 0;
		}

		return $refusal;

	}
	// 診断結果の表示
	public static function shortcode_result_view($content='', $arr=array()){

		$get = self::get_escape();
		$message = '';

		if((!empty($_GET['osdgid']) || !empty($arr['id'])) && isset($get['osdgl']) && isset($get['osdgn'])){
			//
			if(!empty($_GET['osdgid'])){
				$data_id = self::h($_GET['osdgid']);
			}elseif(!empty($arr['id'])){
				$data_id = $arr['id'];
			}
			// データ
			$data = DiagnosisSqlClass::get_diagnosis($data_id);
			$result = DiagnosisResultClass::result_data_arrangement($get, $data);
			$message .= DiagnosisMessageClass::updateMessage('1');
			include(OSDG_PLUGIN_INCLUDE_FILES."/user-viewResultPage.php");

		}

		return $content;

	}
	/*
	*  POST時の処理
	*/
	//
	public static function diagnosis_post($data='', $post=''){

		$return_data = array('line', 'img');
		$name = self::h($_POST['diagnosis_name']);
		// システムに任せた診断
		if(isset($data['diagnosis_type']) && $data['diagnosis_type']==0){
			// 処理
			$hash_array = DiagnosisResultClass::hash_len($name);
			$result_text = DiagnosisResultClass::result_text_arr($data); // Text系
			$result_text_img = DiagnosisResultClass::result_text_arr($data, 'image'); // Image系
			$return_data = DiagnosisResultClass::result_system($result_text, $result_text_img, $hash_array);
		// 設問形式の診断
		}elseif(isset($data['diagnosis_type']) && $data['diagnosis_type']==1){
			// 処理
			$return_data = DiagnosisResultClass::result_qsystem($post, $data);
		}

		return $return_data;

	}
	/*
	*  POST時に404となるのを防ぐ処理
	*/
	public static function init_diagnosis_mode(){

		global $osdg_option_data;
		// 設定されている場合
		if(!empty($osdg_option_data['not404'])){
			// POST時
			if(!empty($_POST['diagnosis_plugin'])){
				$post_uniqid = (isset($_POST['uniq'])) ? $_POST['uniq']: '';
				$nonce_action = OSDG_NONCE_ACTION;
				$nonce_name = OSDG_NONCE_NAME;
				$error_html = '<!DOCTYPE html>'."\n";
				$error_html .= '<html lang="ja">'."\n";
				$error_html .= '<head>'."\n";
				$error_html .= "\t".'<meta charset="UTF-8">'."\n";
				$error_html .= '</head>'."\n";
				$error_html .= '<body>'."\n";
				$error_html .= '{error_message}'."\n";
				$error_html .= '<p><button onclick="javascript:history.back()">戻る</button></p>'."\n";
				$error_html .= '</body>'."\n";
				$error_html .= '</html>'."\n";
				// nonceの認証成功
				if(isset($_POST[$nonce_name]) && wp_verify_nonce($_POST[$nonce_name], $nonce_action)){
					$post = self::post_escape();
					//
					if(!empty($post['diagnosis_id'])){
						$data_id = $post['diagnosis_id'];
						// データ
						$data_arr = DiagnosisSqlClass::get_diagnosis_plurality($data_id);
						// 投稿権限チェック
						$post_authority = self::post_authority_check($data_id, $data_arr);
						// 権限OKなら
						if($post_authority==0){
							$validate = self::validation_post($post); // バリデーション
							// 通過
							if($result = DiagnosisValidationClass::validates($validate)){ // チェックOK
								$check_data = DiagnosisSqlClass::get_diagnosis($data_id); // データ取得
								$return_data = self::diagnosis_post($check_data, $post); // データを元に解析
								$url_name = urlencode($post['diagnosis_name']);
								// 成功なら
								if(!empty($return_data['line']) || !empty($return_data['img'])){
									// 結果表示が別ページに設定されていれば
									if(!empty($check_data['display_flag']) && !empty($check_data['result_page']) && !empty($check_data['result_page_url'])){
										$jump_url = trim($check_data['result_page_url']);
									}else{
										$jump_url = '';
									}
									//
									$return_point = (isset($return_data['point'])) ? $return_data['point'] : '';
									$url = self::url_plus(array('osdgl'=>$return_data['line'], 'osdgimg'=>$return_data['img'], 'osdgid'=>$data_id, 'osdgn'=>$url_name, 'osdgpp'=>$return_point), $jump_url);
								}else{
									$url = self::url_plus(array('msg'=>'dg-error'));
								}
								// リダイレクト
								self::os_redirect($url);
							}else{ // チェックNG
								mb_internal_encoding("UTF-8");
								$error_html = str_replace('{error_message}', DiagnosisValidationClass::pmessage($validate), $error_html);
								print $error_html;
								exit;
							}
						}else{ // 投稿権限NG
							mb_internal_encoding("UTF-8");
							$error_html = str_replace('{error_message}', "このフォームは登録ユーザのみ使用できます。", $error_html);
							print $error_html;
							exit;
						}
					}
				}else{ // nonce認証失敗
					mb_internal_encoding("UTF-8");
					$error_html = str_replace('{error_message}', "POST認証に失敗しました。", $error_html);
					print $error_html;
					exit;
				}
			}
		}

	}
	/*
	*  nonceを再び取得し、出力する（ページキャッシュ対策）
	*/
	public static function again_nonce_mode(){

		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=='osdg-again-nonce'){
			$nonce_action = OSDG_NONCE_ACTION;
			$nonce = wp_create_nonce($nonce_action);
			print $nonce;
			exit;
		}

	}
	/*
	*  Javascript CSS 呼び出し
	*/
	public static function os_wp_enqueue(){

		if(self::has_shortcode('OSDGSIS-FORM')==TRUE || self::has_shortcode('OSDGSIS-RESULT-FORM')==TRUE){
			// jQuery
			wp_enqueue_script('jquery');
			// Javascript
			$dir_ex = explode("/", rtrim(OSDG_PLUGIN_DIR, "/")); // 現在のプラグインのパス
			$now_plugin = end($dir_ex); // 現在のプラグインのディレクトリ名
			wp_enqueue_script('j', plugins_url($now_plugin).'/js/j.js', array(), '1.0');
			// css
			wp_enqueue_style('style', plugins_url($now_plugin).'/css/style.css', array(), '1.0');
		}

	}
	// ショートコードがあるか否か
	public static function has_shortcode($shortcode){

		global $wp_query;
		// 記事データを取得
		if(isset($wp_query->post)){
			$post = $wp_query->post;
			if(isset($post->post_content)){
				$post_data = $post->post_content;
			}
		}
		//　取得できなければ別の配列からもう一度試みる
		if(isset($wp_query->posts) && !isset($post_data)){
			$posts = $wp_query->posts;
			if(isset($posts[0]) && isset($posts[0]->post_content)){
				$post_data = $posts[0]->post_content;
			}
		}
		// 投稿データにショートコードが含まれるか
		if(isset($post_data)){
			// 含めばTRUE
			if(stristr($post_data, "[".$shortcode)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}

	}
	/*
	*  ビューで使用する関数
	*/
	public static function if_empty_checked($str=''){

		if(empty($checked)){
			return 'checked';
		}else{
			return '';
		}

	}
	public static function if_ecall_checked($str1='', $str2=''){

		if($str1==$str2){
			return 'checked';
		}else{
			return '';
		}

	}
	/*
	*  バリデーションチェック
	*/
	// 診断バリデーション
	public static function validation_post($post=''){

		$validate = array();

		foreach($post as $key => $p){
			switch($key){
				case 'diagnosis_name':
					// 空はNG、文字数は50文字まで
					$this_validate = DiagnosisValidationClass::validation_rule($p, $key, array('empty', array('number', 0, 50)));
					break;
				case 'question':
					$this_validate = self::question_validation_post($p);
					break;
			}
			// 結合
			if(!empty($validate)){
				$validate = array_merge($validate, $this_validate);
			}else{
				$validate = $this_validate;
			}
		}

		// メッセージを修正
		$change_arr = array(
			'diagnosis_name'=>'名前',
		);
		$validate = DiagnosisValidationClass::validates_message($validate, $change_arr);

		return $validate;

	}
	// 上記で使用
	public static function question_validation_post($post=''){

		foreach($post as $key => $p){
			// 空はNG
			$this_validate = DiagnosisValidationClass::validation_rule($p, 'question'.$key, array('select-empty'));
			// 結合
			if(!empty($validate)){
				$validate = array_merge($validate, $this_validate);
			}else{
				$validate = $this_validate;
			}
		}
		// メッセージを修正
		$change_arr = array(
			'question'=>'問',
		);
		$validate = DiagnosisValidationClass::validates_message($validate, $change_arr);

		return $validate;

	}

}
?>
