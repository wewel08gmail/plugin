<?php
/*
Plugin Name: User Notes
Plugin URI: http://www.facebook.com/wewel777
Description: User Notes add shortocde [rs_user_notes]
Version: 1.0
Author: john
Author URI: http://www.facebook.com/wewel777
*/

class rs_user_notes {
	public function __construct(){
		add_action('init', array($this, 'init'));
		add_action( 'wp_ajax_rs_save_notes', array($this, 'save_notes'));
		add_action( 'wp_ajax_rs_save_read_status', array($this, 'save_read_status'));
	}
	public function save_notes() {
		global $wpdb;
		$status=0;
		$message="No Data was saved.";
		if(is_user_logged_in()){
			if(isset($_POST["rs_notes"]) && isset($_POST["rs_post_id"])){
				$current_user = wp_get_current_user();
				$post_id=absint($_POST["rs_post_id"]);
				$comments=sanitize_text_field(esc_attr($_POST["rs_notes"]));
				update_user_meta($current_user->ID, 'rs_user_notes_'.$post_id, $comments);
				$status=1;
				$message="Saved!";
			}
		}
		echo json_encode(array(
			"status"=>$status,
			"message"=>$message
		));
		wp_die();
	}
	public function save_read_status() {
		global $wpdb;
		$status=0;
		$message="No Data was saved.";
		if(is_user_logged_in()){
			if(isset($_POST["rs_read_status"]) && isset($_POST["rs_post_id"])){
				$current_user = wp_get_current_user();
				$post_id=absint($_POST["rs_post_id"]);
				$read_status=absint($_POST["rs_read_status"]);
				if($read_status==1){
					add_user_meta($current_user->ID, 'rs_user_read_status_'.$post_id, '1');
				}
				else{
					delete_user_meta($current_user->ID, 'rs_user_read_status_'.$post_id);
				}
				$status=1;
				$message="Saved!";
			}
		}
		echo json_encode(array(
			"status"=>$status,
			"message"=>$message
		));
		wp_die();
	}
	public function init(){
		add_shortcode('rs_user_notes', array($this, 'user_notes'));
	}
	public function user_notes($atts){
		global $post;
		if(is_user_logged_in()){
			$current_user = wp_get_current_user();
			$rs_read_status=get_user_meta($current_user->ID, 'rs_user_read_status_'.$post->ID, true);
			$rs_notes=get_user_meta($current_user->ID, 'rs_user_notes_'.$post->ID, true);
			wp_enqueue_script('jquery');
			add_action('wp_footer', array($this, 'rs_print_script'), 0, 100);
			$content='<div class="wct-user-notes" data-postid="'.$post->ID.'">
				'.((!isset($atts["module"]) || $atts["module"]=="tickbox")?'<div class="wct-tickbox '.(($rs_read_status==1)?"wct-tickbox-ticked":"wct-tickbox-unticked").'">
					<span class="wct-tick-icon"></span>
					<span class="wct-tick-text wct-tick-text-unticked">Tick this box if you have read/watched this content.</span>
					<span class="wct-tick-text wct-tick-text-ticked">Content viewed.</span>
				</div>':'').'
				'.((!isset($atts["module"]) || $atts["module"]=="notes")?'<div class="wct-notes">
					<h4>Notes</h4>
					<textarea placeholder="Write your notes here" class="wct-notes-textarea">'.esc_textarea($rs_notes).'</textarea>
					<div class="wct-actions">
						<input value="Save" class="wct-save-btn" type="button">
						<span class="wct-result"></span>
					</div>
				</div>':'').'
			</div>
			<style>
			
			.wct-user-notes {
				clear: both;
				margin: 10px 0;
			}
			.wct-tickbox {
				cursor: pointer;
				margin-bottom: 15px;
			}
			.wct-tick-icon {
				background-color: #ddd;
				border-radius: 3px;
				display: block;
				float: left;
				height: 20px;
				margin-right: 10px;
				width: 20px;
			}
			.wct-tick-text {
				display: block;
				float: left;
				height: 20px;
				line-height: 20px;
			}
			.wct-tickbox::after {
				clear: both;
				content: "";
				display: block;
			}
			.wct-tickbox-unticked .wct-tick-text-unticked {
				display: block;
			}
			.wct-tickbox-unticked .wct-tick-text-ticked {
				display: none;
			}
			.wct-tickbox-ticked .wct-tick-text-unticked {
				display: none;
			}
			.wct-tickbox-ticked .wct-tick-text-ticked {
				display: block;
			}
			.wct-tickbox-ticked .wct-tick-icon {
				background-color: #183d6e;
			}
			.wct-tickbox-ticked .wct-tick-icon::before {
				color: #fff;
				content: "\2714";
				display: block;
				height: 100%;
				text-align: center;
				width: 100%;
			}
			.wct-notes {
				border: 1px solid #ddd;
				border-radius: 3px;
				padding: 10px;
			}
			.wct-notes h4 {
				background-color: #eee;
				font-size: 15px;
				margin: -10px -10px 10px;
				padding: 10px;
			}
			.wct-notes textarea {
				box-sizing: border-box;
				height: 100px;
				margin-bottom: 10px;
				width: 100%;
			}
			.wct-save-btn {
				background-color: #183d6e;
				border: 0 none;
				border-radius: 3px;
				color: #fff;
				float: right;
				height: 28px;
				padding: 0 20px;
			}
			.wct-result {
				display: block;
				float: right;
				line-height: 28px;
				margin-right: 10px;
			}
			.wct-notes {
			}
			.wct-actions::after {
				clear: both;
				content: "";
				display: block;
			}
			.wct-actions {
				border-top: 1px solid #ddd;
				margin: 0 -10px;
				padding: 10px 10px 0;
			}
			.wct-err {
				color: #f00;
			}
			.wct-msg {
				color: #338d11;
			}
			
			</style>';
			return $content;
		}
	}
	public function rs_print_script(){
		?>
		<script>
        jQuery(document).ready(function(){
			$rs_ajax_url='<?php echo admin_url( 'admin-ajax.php');?>';
			if(jQuery('.wct-user-notes').length>0){
				jQuery('.wct-tickbox').click(function(){
					$this=jQuery(this);
					$rs_post_id=$this.parents('.wct-user-notes').data('postid');
					if($this.hasClass('wct-tickbox-unticked')){
						$this.removeClass('wct-tickbox-unticked');
						$this.addClass('wct-tickbox-ticked');
						$rs_tick_status=1;
					}
					else{
						$this.removeClass('wct-tickbox-ticked');
						$this.addClass('wct-tickbox-unticked');
						$rs_tick_status=0;
					}
					var $rs_data = {
						'action': 'rs_save_read_status',
						'rs_post_id': $rs_post_id,
						'rs_read_status': $rs_tick_status
					};
					jQuery.post($rs_ajax_url, $rs_data, function($rs_response) {
						$rs_response_data=JSON.parse($rs_response);
						if($rs_response_data.status!="1"){
							
						}
					});
				});
				jQuery('.wct-user-notes .wct-save-btn').click(function(){
					$this=jQuery(this);
					$rs_post_id=$this.parents('.wct-user-notes').data('postid');
					$rs_notes=$this.parents('.wct-user-notes').find('.wct-notes-textarea').val();
					$this.parents('.wct-user-notes').find('.wct-result').html('Saving...');
					var $rs_data = {
						'action': 'rs_save_notes',
						'rs_post_id': $rs_post_id,
						'rs_notes': $rs_notes
					};
					jQuery.post($rs_ajax_url, $rs_data, function($rs_response) {
						$rs_response_data=JSON.parse($rs_response);
						if($rs_response_data.status=="1"){
							$this.parents('.wct-user-notes').find('.wct-result').empty().html('<span class="wct-msg">'+$rs_response_data.message+'</span>');
						}
						else{
							$this.parents('.wct-user-notes').find('.wct-result').empty().html('<span class="wct-err">'+$rs_response_data.message+'</span>');
						}
					});
				});
			}
		});
        </script>
		<?php
	}
}
$rs_user_notes=new rs_user_notes();