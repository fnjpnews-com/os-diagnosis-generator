/**
*  default-one theme
*  defaultテーマをベースに、問を1つずつ表示します。
**/
// バリデーションチェック
function dg_valid(point){
	jQuery('.form-message').html('');
	//
	switch(point){
		// 名前チェック
		case 0:
			if(!jQuery('#diagnosis-name').val()){
				jQuery('.form-message').html('<p>名前を入力してください！</p>');
				return false;
			}
			else{
				return true;
			}
			break;
		// 選択肢
		default:
			var num = jQuery('.diagnosis-wrap .question').length;
			// 最後なら
			if(num<point){
				return true;
			}
			else{
				var checked_val = parseInt(jQuery('input[name="question['+point+']"]:checked').val());
				// 
				if(0<checked_val){
					return true;
				}
				else{
					jQuery('.form-message').html('<p>選択してください！</p>');
					return false;
				}
			}
			break;
	}
	return false;
}
// ボタン及び設問文を表示
function view_question(){
	jQuery('.diagnosis-wrap .question').hide();
	var num = jQuery('.diagnosis-wrap .question').length;
	var point = parseInt(jQuery('#diagnosis-point').val());
	var finish_flag = 0; // 最後の問を表示したかどうか
	// 設問数よりポイントが上回っているなら
	if(num<point){
		finish_flag = 1;
	}
	// 最後の設問じゃなければ入力を表示
	if(finish_flag==0){
		// 初回
		if(0==point){ // 名前入力と次へボタンを表示
			jQuery('.label-diagnosis-name').show();
			jQuery('#diagnosis-name').show();
			jQuery('.diagnosis-form #back-button').hide();
			jQuery('.diagnosis-form #next-button').show();
		}
		else{ // 設問文章と戻るボタンと次へボタンを表示
			jQuery('.label-diagnosis-name').hide();
			jQuery('#diagnosis-name').hide();
			jQuery('.diagnosis-form #back-button').show();
			jQuery('.diagnosis-form #next-button').show();
			// 設問表示
			jQuery('.diagnosis-form #block-question'+point).show();
		}
		jQuery('.diagnosis-form .sbm-button').hide();
	}
	else{ // 最後なら
		jQuery('.form-message').html('<p>問題がなければ、下のボタンを押してください。</p>');
		jQuery('.label-diagnosis-name').hide();
		jQuery('#diagnosis-name').hide();
		jQuery('.diagnosis-form #back-button').show();
		jQuery('.diagnosis-form #next-button').hide();
		jQuery('.diagnosis-form .sbm-button').show();
	}
}
// 読込時
jQuery(document).ready(function(){
	jQuery('.diagnosis-wrap .question').hide();
	view_question();
});
// 次へボタンクリック時
jQuery(document).on('click', '#next-button', function(){
	var point = parseInt(jQuery('#diagnosis-point').val());
	// チェック
	if(dg_valid(point)!==false){
		point = point + 1;
		jQuery('#diagnosis-point').val(point);
		view_question();
	}
});
// 戻るボタンクリック時
jQuery(document).on('click', '#back-button', function(){
	var point = parseInt(jQuery('#diagnosis-point').val());
	point = point - 1;
	jQuery('#diagnosis-point').val(point);
	view_question();
});