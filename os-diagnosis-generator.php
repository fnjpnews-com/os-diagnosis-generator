<?php
/*
Plugin Name: 診断ジェネレータ作成プラグイン
Plugin URI: http://lp.olivesystem.jp/plugin-dg
Description: WordPressで診断ジェネレータ（診断サイト、占いサイト）を作成できるプラグインです
Version: 1.4.10
Author: OLIVESYSTEM（オリーブシステム）
Author URI: http://lp.olivesystem.jp/
*/
if(!isset($wpdb)){
	global $wpdb;
}
// 現在のプラグインバージョン
define('OSDG_PLUGIN_VERSION','1.4.10');
// 現在のテーブルバージョン
define('OSDG_PLUGIN_TABLE_VERSION','1.3');
// DBにデータを保存する項目名
define('OSDG_PLUGIN_VERSION_NAME','os_diagnosis_generator_PluginVersion');
define('OSDG_PLUGIN_TABLE_VERSION_NAME','os_diagnosis_generator_PluginTableVersion');
define('OSDG_PLUGIN_DATA_NAME','os_diagnosis_generator_Plugin');
// テーブル名
define('OSDG_PREFIX', $wpdb->prefix); // プレフィックス
define('OSDG_PLUGIN_TABLE_NAME', OSDG_PREFIX.'os_diagnosis_generator_data');
define('OSDG_PLUGIN_DETAIL_TABLE_NAME', OSDG_PREFIX.'os_diagnosis_generator_detail_data');
define('OSDG_PLUGIN_QUESTION_TABLE_NAME', OSDG_PREFIX.'os_diagnosis_generator_question_data');
define('OSDG_PLUGIN_FORM_OPTIONS', OSDG_PREFIX.'os_diagnosis_generator_form_options');
// wp_nonce用
define('OSDG_NONCE_ACTION', 'osdg_form_action'); // action
define('OSDG_NONCE_NAME', 'osdg_form_nonce'); // name
// このファイル
define('OSDG_PLUGIN_FILE', __FILE__);
// プラグインのディレクトリ
define('OSDG_PLUGIN_DIR', plugin_dir_path(__FILE__));
// テキストメインのPHPファイルをいれているディレクトリ
define('OSDG_PLUGIN_INCLUDE_FILES', OSDG_PLUGIN_DIR.'include_files');
// グローバル変数
$osdg_user_data = '';
$osdg_sqlfile_check = '';
$osdg_option_data = '';
$osdg_cache_data = '';
$pro_flag = 0;
// 関数
include OSDG_PLUGIN_DIR."function.php";
// 時刻のタイムゾーンを設定
osdgTimezoneSet();
// 他バージョンからの読み込みでなければ
if(!defined('OSDGPRO_PLUGIN_DIR')){
	// classを読み込み、整理
	include(OSDG_PLUGIN_DIR."class/messageClass.php");
	class DiagnosisMessageClass extends PreDiagnosisMessageClass{}
	include(OSDG_PLUGIN_DIR."diagnosisClass.php");
	class DiagnosisClass extends PreDiagnosisClass{}
	include(OSDG_PLUGIN_DIR."class/validationClass.php");
	class DiagnosisValidationClass extends PreDiagnosisValidationClass{}
	include(OSDG_PLUGIN_DIR."class/sqlClass.php");
	class DiagnosisSqlClass extends PreDiagnosisSqlClass{}
	//include(OSDG_PLUGIN_DIR."class/resultMailClass.php");
	//class DiagnosisResultMailClass extends PreDiagnosisResultMailClass{}
	include(OSDG_PLUGIN_DIR."class/resultClass.php");
	class DiagnosisResultClass extends PreDiagnosisResultClass{}
	include(OSDG_PLUGIN_DIR."class/themeClass.php");
	class DiagnosisThemeClass extends PreDiagnosisThemeClass{}
	include(OSDG_PLUGIN_DIR."diagnosisViewClass.php");
	class DiagnosisView extends PreDiagnosisView{}
	include(OSDG_PLUGIN_DIR."diagnosisAdminClass.php");
	class DiagnosisAdmin extends PreDiagnosisAdmin{}
	$diagnosisViewClass = new DiagnosisView();
	$diagnosisAdminClass = new DiagnosisAdmin();
}else{
	$pro_flag = 1;
}