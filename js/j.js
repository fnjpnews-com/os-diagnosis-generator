//var j = jQuery.noConflict();
// メッセージ
function open_message(ids, vtext){
	var ok_text = '<span onclick="close_message(\''+ids+'\')" class="pointer-l">OK</span>';
	jQuery('#'+ids).html('<div id="'+ids+'" class="green">'+vtext+ok_text+'</div>');
}
// メッセージを閉じる
function close_message(ids){
	jQuery('#'+ids+'').html('<div id="'+ids+'"></div>');
}
// 要素を表示
function display_block(ids){
	jQuery('#'+ids+'').css('display', 'block');
}
// 要素を非表示
function display_none(ids){
	jQuery('#'+ids+'').css('display', 'none');
}
// クリア
function text_clear(ids){
	jQuery('#'+ids+'').val('');
}