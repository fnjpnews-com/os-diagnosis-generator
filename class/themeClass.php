<?php
// フォームテーマを操作するclass
class PreDiagnosisThemeClass extends DiagnosisClass {

	public function __construct(){

		parent::__construct();

	}
	/*
	*  管理画面側
	*/
	// 編集したテーマを保存
	public static function save_themefile(){

		if(!empty($_POST['theme_name'])){
			$dir = OSDG_PLUGIN_DIR.'theme/'.esc_html($_POST['theme_name']).'/';
			// CSSファイルを更新、保存
			if(isset($_POST['css'])){
				$save_return = self::save($dir.'style.css', $_POST['css']);
				if($save_return===FALSE){
					return false;
				}
			}
			// jsファイルを更新、保存
			if(isset($_POST['js'])){
				$save_return = self::save($dir.'theme.js', $_POST['js']);
				if($save_return===FALSE){
					return false;
				}
			}
			return true;
		}else{
			return false;
		}

	}
	// 保存処理
	// 失敗の場合FALSE、成功ならバイト数
	private function save($file_pass='', $data=''){

		$data = stripslashes($data);
		// PHPバージョンが5以上なら
		if(self::phpversion_check(5)==TRUE){
			$return = file_put_contents($file_pass, $data);
		}else{ // それ以下ならfopen
			$fp = fopen($file_pass, 'w');
			$return = fwrite($fp, $data);
			fclose($fp);
		}

		return $return;

	}
	// 指定テーマのcssを出力
	public static function print_theme_css($dir='default'){

		$file = self::read_file($dir, 'style.css');
		print $file;
		exit;

	}
	// 指定テーマのjsを出力
	public static function print_theme_js($dir='default'){

		$file = self::read_file($dir, 'theme.js');
		print $file;
		exit;

	}
	// 上記2つの読み込み箇所
	public static function read_file($dir='', $filename=''){

		$file_pass = OSDG_PLUGIN_DIR.'theme/'.$dir.'/'.$filename;
		// PHP5以上なら
		if(self::phpversion_check(5)){
			$data = file_get_contents($file_pass, FILE_USE_INCLUDE_PATH);
		}else{
			$data = file_get_contents($file_pass, true);
		}

		return trim($data);

	}
	// 新規作成
	public static function add_themefile(){

		if(!empty($_POST['add_name'])){
			$dir = OSDG_PLUGIN_DIR.'theme/'.esc_html($_POST['add_name']).'/';
			// ディレクトリ作成
			if(mkdir($dir, 0777, true)){
				$file_css = "/*\n* ".esc_html($_POST['add_name'])."\n*/\n";
				$file_js = "/*\n* ".esc_html($_POST['add_name'])."\n*/\n";
				// コピーがあれば
				if(isset($_POST['add_type']) && $_POST['add_type']=='copy'){
					$copy_theme = (isset($_POST['copy_theme'])) ? $_POST['copy_theme']: ''; // コピー元
					$file_css .= self::read_file($copy_theme, 'style.css');
					$file_js .= self::read_file($copy_theme, 'theme.js');
				}
				// cssファイルを作成
				if(touch($dir.'style.css')){
					// データ保存
					$save_return = self::save($dir.'style.css', $file_css);
					if($save_return===FALSE){
						return false;
					}
				}else{
					return false;
				}
				// jsファイルを作成
				if(touch($dir.'theme.js')){
					// データ保存
					$save_return = self::save($dir.'theme.js', $file_js);
					if($save_return===FALSE){
						return false;
					}
				}else{
					return false;
				}
				// 作成完了したらパーミッション変更
				chmod($dir, 0755);
				return true;
			}else{ // ディレクトリ作成失敗
				return false;
			}
		}else{
			return false;
		}

	}

}
?>