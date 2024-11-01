<?php

/*
Plugin Name: Woocommerce Wrong Coupon Notification
Plugin URI: http://rakibh.com
Description: This Plugin Notify Site Admin Via Email If Wrong Coupon Code is Used in Woocoommerce store.
Version: 0.5
Author: Rakib Hasan
Author URI: https://rakibh.com
License: GPL2
*/

class Woo_Wrong_Coupon_Notify{
	private $error_message;
	private $error_code;
	private $coupon_code;
	private $wc_active;
	
	public function __construct() {
		add_action('woocommerce_coupon_error',array($this,'capture_coupon_error'),10,3);
		add_action( 'admin_init', array($this,'wc_check') );


	}


	public function wc_check(){
		if ( !class_exists( 'woocommerce' ) ) {

			add_filter('admin_notices', array($this,'wc_admin_notice'));
		}
	}

	function wc_admin_notice(){
			?>
			<div class="notice notice-error is-dismissible">
				<p>WooCommerce is not activated, please activate it to use <b> Woocommerce Wrong Coupon Notification Plugin</b></p>
			</div>
			<?php
	}


	public function capture_coupon_error( $err, $err_code, $coupon ) {
		$this->error_message=$err;
		$this->error_code=$err_code;
		$this->coupon_code=$coupon->code;
		$messgae=$this->error_message();
		$this->send_admin_notification($messgae);
		return $err;
	}

	protected function error_message() {
		$message="<h1>Wrong Coupon Code Used On ".get_option('blogname')."</h1> <h3>Details</h3>";
		$message.="<p>Time: ".date("Y-m-d h:i:sa")."</p>";
		$message.="<p>Coupon Code: ".$this->coupon_code."</p>";
		$message.="<p>Error Message: ".$this->error_message."</p>";
		$message.="<p>Error Code: ".$this->error_code."</p>";
		return $message;
	}

	protected function send_admin_notification($message) {
		$to=get_option('admin_email');
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$subject= "Wrong Coupon Notification for - ". get_option('blogname');
		wp_mail($to,$subject,$message,$headers);


	}

}
$wrcn_woo_wrong_coupon=new Woo_Wrong_Coupon_Notify();