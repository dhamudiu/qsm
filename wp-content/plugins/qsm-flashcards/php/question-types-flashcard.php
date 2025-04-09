<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_action( 'init', 'qsm_flashcard' );

function qsm_flash_card_review( $id, $question, $answers ) {
	$return_array    = array(
		'points'       => 0,
		'user_text'    => '',
		'correct_text' => '',
		'user_question'=> '',
		'user_answer'  => array(),
	);

	$user_ans_array  = isset( $_POST[ 'question' . $id ] ) ?  sanitize_text_field( wp_unslash( $_POST[ 'question' . $id ] ) ) : "";
	$return_array['user_answer']    = $user_ans_array;
	$ans          = explode( ',,', $answers[0][0]);
	$user_question = htmlspecialchars_decode( $ans[0], ENT_QUOTES );
	$user_answers = htmlspecialchars_decode( $ans[1], ENT_QUOTES )."|.".htmlspecialchars_decode( $user_ans_array, ENT_QUOTES );
	$correct_ans  = htmlspecialchars_decode( $ans[1], ENT_QUOTES );
	$return_array['points'] = 1 === intval($user_ans_array) ? $answers[0][1] : 0;

	if ( 1 === intval($user_ans_array) ) {
		$return_array['correct'] = 'correct';
	}
	$return_array['user_question']       .= $user_question ;
	$return_array['user_text']          .= $user_question . ',,' . $correct_ans;
	$return_array['correct_text']       .= $correct_ans ;
	return apply_filters( 'qsm_flash_card_review_after', $return_array, $answers );
}
function qsm_flashcard() {
	global $mlwQuizMasterNext;
	$flashcard_options = array(
		'show_answer_option'                 => true,
		'show_correct_answer_info'           => true,
		'show_change_answer_editor'          => true,
		'show_flashcard_buttons'			 => true,
		'show_match_answer'                  => false,
		'show_autofill'                      => false,
		'show_limit_text'                    => false,
		'show_limit_multiple_response'       => false,
		'show_file_upload_type'              => false,
		'show_file_upload_limit'             => false,
		'multiple_choise'                    => true,
		'description'                        => null,
		'use_custom_default_template'        => true,
		'use_custom_user_answer_template'    => true,
		'use_custom_correct_answer_template' => true,
	);

	$mlwQuizMasterNext->pluginHelper->register_question_type( esc_html__( 'Flash card', 'qsm_flashcards' ), 'qsm_flash_card_display', true, 'qsm_flash_card_review', null, null, 18, $flashcard_options );
	$mlwQuizMasterNext->pluginHelper->set_question_type_meta( 18, 'category', 'Flashcard' );
}
function qsm_flashcard_template() {
	wp_enqueue_script( 'qsm_flashcards_admin_script', plugins_url( '../js/qsm-flashcards-questiontype-admin.js', __FILE__ ), array( 'qsm_admin_js' ), QSM_FLASHCARDS_VERSION ,true);
	wp_enqueue_style( 'qsm_flashcards_admin_css', plugins_url( '../css/qsm-flashcards.css', __FILE__ ), array(), QSM_FLASHCARDS_VERSION);
	?>
	<script type="text/template" id="tmpl-flashcard-questiontype">
		<div class="answers-single qsm-flashcard-questiontype">
			<div class="answer-text-div">
				<#
					let answer = data.answer;
					let answer_array = ["",""];
					if(answer != null){
						answer_array = answer.split(',,');
					}
					let caption = data.caption;
					let caption_array = ["",""];
					if(caption != null){
						caption_array = caption.split(',,');
					}
				#>
				<# if ( 'rich' == data.answerType ) { #>
					<textarea class="answer-textarea-front" id="answer-{{data.question_id}}-{{data.count}}-front"></textarea>
				<# } else if ( 'image' == data.answerType ) { #>
					<input type="text" class="answer-text answer-text-front" id="featured_image_textbox_front" value="{{answer_array[0]}}" placeholder="<?php esc_attr_e( 'Insert front image URL', 'qsm_flashcards' ); ?>"/>
					<a href="javascript:void(0)" class="set_featured_image" id="set_featured_image_front"><span class="dashicons dashicons-insert"></span></a>
					<input type="text" class="answer-caption answer-caption-front" id="featured_image_caption_front" value="{{caption_array[0]}}" placeholder="<?php esc_attr_e( 'Image Caption', 'qsm_flashcards' ); ?>"/>
				<# } else { #>
					<input type="text" class="answer-text answer-text-front" value="{{answer_array[0]}}" placeholder="<?php esc_attr_e( 'Front', 'qsm_flashcards' ); ?>"/>
				<# } #>
			</div>
			<div class="answer-text-div">
				<# if ( 'rich' == data.answerType ) { #>
					<textarea class="answer-textarea-back" id="answer-{{data.question_id}}-{{data.count}}-back"></textarea>
				<# } else if ( 'image' == data.answerType ) { #>
					<input type="text" class="answer-text answer-text-back" id="featured_image_textbox_back" value="{{answer_array[1]}}" placeholder="<?php esc_attr_e( 'Insert back image URL', 'qsm_flashcards' ); ?>"/>
					<a href="javascript:void(0)" class="set_featured_image" id="set_featured_image_back"><span class="dashicons dashicons-insert"></span></a>
					<input type="text" class="answer-caption answer-caption-back" id="featured_image_caption_" value="{{caption_array[1]}}" placeholder="<?php esc_attr_e( 'Image Caption', 'qsm_flashcards' ); ?>"/>
				<# } else { #>
					<input type="text" class="answer-text answer-text-back" value="{{answer_array[1]}}" placeholder="<?php esc_attr_e( 'Back', 'qsm_flashcards' ); ?>"/>
				<# } #>
			</div>
			<# if ( 0 == data.form_type ) { #>
				<# if ( 1 == data.quiz_system || 3 == data.quiz_system ) { 
					#>
					<div class="answer-point-div"><input type="text" class="answer-points" value="{{data.points}}" placeholder="Points"/></div>
				<# } #>
			<# } else { #>
					<div class="answer-point-div"><input type="text" class="answer-points" value="{{data.points}}" placeholder="Points"/></div>
			<# } #>
			<?php do_action( 'qsm_admin_single_answer_option_fields' ); ?>
		</div>
		</div>
	</script>
	<?php
}
add_action( 'qsm_admin_after_single_answer_template', 'qsm_flashcard_template' );
/**
 * The qsm_result_question_types filter callback function.
 *
 * @param array
 **/
function qsm_flashcard_question_result_question_types( $result_question_types ) {
	array_push( $result_question_types, 18 );
	return $result_question_types;
}
add_filter( 'qsm_result_question_types', 'qsm_flashcard_question_result_question_types' );

function qsm_flash_card_display( $id, $question, $answers ) {
	global $mlwQuizMasterNext;
	wp_enqueue_script( 'qsm_flashcards_script', plugins_url( '../js/qsm-flashcards-questiontype.js', __FILE__ ), array(), '1.1.0' );
	wp_enqueue_style( 'qsm_flashcards_style', plugins_url( '../css/qsm-flashcards-questiontype.css', __FILE__ ), array(), '1.1.0' );
	$answer_editor      = $mlwQuizMasterNext->pluginHelper->get_question_setting( $id, 'answerEditor' );
	$required           = $mlwQuizMasterNext->pluginHelper->get_question_setting( $id, 'required' );
	$new_question_title = $mlwQuizMasterNext->pluginHelper->get_question_setting( $id, 'question_title' );
	qsm_question_title_func( $question, 'flash_card', $new_question_title, $id );
	if ( 0 == $required ) {
		$require_class = 'mlwRequiredRadio';
	} else {
		$require_class = '';
	}
	$newarray = array();
	$display = ''; ?>
	 <div class="cards cards_<?php echo $id;?>">
	<?php
	foreach ($answers as $answer_index => $answer ) {
		$ans = explode( ',,', $answer[0] );
		$buttonsection = "<div class='buttonsdiv ". $require_class ."'>";
		$buttonsection .= "<label class='hidediv'><span class='dashicons dashicons-hidden spanclass'></span></label>";
		$buttonsection .= "<label class='cardbtns incorrectdiv'><input type='radio' class='checkbox_class' value='0' name='".esc_attr( 'question' . $id )."'/><span class='dashicons dashicons-dismiss spanclass'></span></label>";
		$buttonsection .= "<label class='cardbtns correctdiv'><input type='radio' class='checkbox_class' value='1' name='".esc_attr( 'question' . $id )."'/><span class='dashicons dashicons-saved spanclass'></span></label>";
		$buttonsection .= "</div>";
		$answer1 = $ans[1].$buttonsection;
		if($answer_editor == "rich"){ $classtype = "richclasstype";}
		else {$classtype = "textclasstype"; }
		?>
		<div class="flip-container <?php echo $classtype; ?>">
		<div  class = "card_questiontype flipper">
			<div class="front_questiontype"><?php echo wpautop( htmlspecialchars_decode( do_shortcode($ans[0]), ENT_QUOTES ) ); ?></div>
			<div class="back_questiontype">
				<div class="scrollable">
					<?php echo wpautop( htmlspecialchars_decode( do_shortcode($answer1), ENT_QUOTES ) ); ?>
				</div>
			</div>
		</div>
	</div>
		<?php
	}
	?>
	 </div>
	<?php
}
/**
 * The qsm_result_page_custom_default_template filter callback function.
 *
 * @arg $total_answers, $questions, $answer
 * return string
 **/
function qsm_flashcard_question_result_page_default_template( $final_ans, $total_answers, $questions, $answer ) {
	global $mlwQuizMasterNext;
	$quiz_options  = $mlwQuizMasterNext->quiz_settings->get_quiz_options();
	$final_ans = !empty($final_ans) ? $final_ans : '';
	if ( 18 == $answer['question_type'] ) {
			$front_back   = explode( ',,', $answer[1]);
			
			$final_ans  .= '<div class="mlw_qmn_new_question"><b>'. htmlspecialchars_decode($front_back[0]).' </b></div><div class="qmn_question_answer">';

			if( 1 == $answer['user_answer'] ){
				$final_ans .= '<span class="qsm-text-correct-option qsm-text-user-correct-answer  ">'.htmlspecialchars_decode($answer[2]).'</span></div>';
			}else if( 0 == $answer['user_answer'] ){
				$final_ans .= '<span class="qsm-text-wrong-option   ">'.htmlspecialchars_decode($answer[2]).'</span></div>';
			} else {
				$final_ans .= '<span class="qsm-text-simple-option   ">'. $quiz_options->no_answer_text .'</span></div>';
			}
	}
	return $final_ans;
}
add_filter( 'qsm_result_page_custom_default_template', 'qsm_flashcard_question_result_page_default_template', 10, 4 );
/**
 * The qsm_flashcard_question_result_page_user_answer_template filter callback function.
 *
 * @arg $question, $answer
 * return string
 **/
function qsm_flashcard_question_result_page_user_answer_template( $final_ans, $questions, $answer ) {
	global $mlwQuizMasterNext;
	$quiz_options  = $mlwQuizMasterNext->quiz_settings->get_quiz_options();
	$final_ans = !empty($final_ans) ? $final_ans : '';
	if ( 18 == $answer['question_type'] ) {
			$front_back   = explode( ',,', $answer[1]);
			$final_ans  .= '<div class="qmn_question_answer">';

			if( 1 == $answer['user_answer'] ){
				$final_ans .= '<span class="qsm-text-correct-option qsm-text-user-correct-answer  ">'.$answer[2].'</span></div>';
			}else if( 0 == $answer['user_answer'] ){
				$final_ans .= '<span class="qsm-text-wrong-option   ">'.$answer[2].'</span></div>';
			}else {
				$final_ans .= '<span class="qsm-text-simple-option   ">'. $quiz_options->no_answer_text .'</span></div>';
			}
	}
	return $final_ans;
}
add_filter( 'qsm_result_page_custom_user_answer_template', 'qsm_flashcard_question_result_page_user_answer_template', 10, 3 );
/**
 * The qsm_flashcard_question_result_page_correct_answer_template filter callback function.
 *
 * @arg $question_type, $answer
 * return string
 **/
function qsm_flashcard_question_result_page_correct_answer_template( $final_ans, $questions, $answer ) {
	$final_ans = !empty($final_ans) ? $final_ans : '';
	if ( 18 == $answer['question_type'] ) {
			$front_back   = explode( ',,', $answer[1]);
			$final_ans  .= '<div class="qmn_question_answer">';
			$final_ans .= '<span class="qsm-text-correct-option   ">'.$answer[2].'</span></div>';
	}
	return $final_ans;
}
add_filter( 'qsm_result_page_custom_correct_answer_template', 'qsm_flashcard_question_result_page_correct_answer_template', 10, 3 );
