<?php

class customer_cart_details
{
	// public $is_discount_eligible = false;
	// public $coupon_code = "exams";
	public function __construct()
	{

		print("inside customer_cart_details... ");

		add_action('woocommerce_cart_calculate_fees', [$this, 'apply_coupon_to_cart'], 10, 1);
		// add_filter('woocommerce_coupons_enabled', [$this, 'hide_coupon_field_on_cart']);
		// add_action( 'template_redirect', [$this, 'quadlayers_add_to_cart_programmatically'] );
		// add_action( 'init', [$this, 'ts_get_custom_coupon_code_to_session'] );
		// add_action('woocommerce_before_cart_table',[$this, 'ts_apply_discount_to_cart'] , 10, 0);
		// add_action( 'woocommerce_before_calculate_totals',[$this, 'divi_engine_number_percentage_price'] );
		add_action('template_redirect', [$this, 'custom_message']);
		// add_action('woocommerce_before_cart', [$this, 'display_message'], 10, 2);
		// // add_filter('woocommerce_cart_totals_coupon_label', [$this, 'discountLabel'], 10, 2);
		// // add_filter('woocommerce_cart_totals_coupon_html', [$this, 'discountHtml'], 10, 3);
		remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
	}
	// function close_custom_alert() {
	// 	echo "hello - testing method.";
	// 	// code to hide alert div
	// }
	// display the external reward message in checkout
	function custom_message()
	{
		if (is_checkout() && !is_wc_endpoint_url()) {
			// <div class="custom-alert" id="comment_custom_alert">
			// 	<div class="alert-success">
			// 		<button type="button" onclick="this.parentNode.parentNode.remove()" class="close">&times;</button>
			// 		<strong>Success!</strong> Your comment has been posted successfully.
			// 	</div>
			// </div>
			wc_add_notice(__('Hey! Buy more and get a exciting offers!!!!<a href="shop"  class="button alt">Shop Now</a>'), 'notice');
		}
	}

	// remove the coupon field in cart page
	function hide_coupon_field_on_cart($enabled)
	{
		if (is_cart()) {
			$enabled = false;
		}
		return $enabled;
	}


	//get the available coupon code from database
	function get_available_coupon_codes()
	{
		global $wpdb;

		// Get an array of all existing coupon codes
		$coupon_codes = $wpdb->get_col("SELECT post_name FROM $wpdb->posts WHERE post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_name ASC");

		// Display available coupon codes
		return $coupon_codes; // always use return in a shortcode
	}


	// API call to get coupon code
	function apply_coupon_to_cart($cart)
	{

		print("inside apply_coupon_to_cart ...");
		// We should apply the discount for the authorized regular customers only
		if (!is_user_logged_in()) {
			return;
		}

		$store_url = get_site_url();
		wc_load_cart();
		$wc_cart = WC()->cart;
		$session_handler = new WC_Session_Handler();

		// Get the user session from its user ID:
		// $session = WC()->session->get_session_cookie();
		$session_id = wp_get_session_token();
		// echo $session;
		// print_r($session);
		// Get cart items array
		// $cart_items = maybe_unserialize($session['cart']);

		$carts = array();
		foreach ($wc_cart->get_cart() as $cart_item_key => $cart_item) {
		
			// 	// stroe the product details here
			$product = $cart_item['data'];
			// 	$customer_name = $wc_cart->get_customer()->get_display_name();
			// 	// $customer_email = $wc_cart->get_customer()->get_email();
			$customer_phone = $wc_cart->get_customer()->get_billing_phone();
			// 	$customer_role = $wc_cart->get_customer()->get_role();
			// 	$product_id_no = $product->get_id();
			// 	$product_name = $product->get_title();
			// 	$quantity = $cart_item['quantity'];
			// 	$subtotal = $wc_cart->get_product_subtotal($product, $cart_item['quantity']);

			$cart_detail = array(
				'productId' => $product->get_id(),
				'productName' => $product->get_title(),
				'quantity' => $cart_item['quantity'],
				// 'price' => $item->get_price(),
			);
			array_push($carts, $cart_detail);
		}
		// // print_r($product);
		// // echo"<pre>";print_r($product);echo"</pre>";
		$body = array(
			'storeId'   => $store_url,
			'storeUrl'  => $store_url,
			'phoneNumber' => $customer_phone,
			'sessionId'  => $session_id,
			// 	// 'orderId'    => $order_id,
			// 'orderDetails' => $carts,
		);
		$body = json_encode($body);
		// $session_id = json_encode($session_id);
		$args = array(
			'body'        => $body,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     =>  [
				'accept'  => 'application/json',
				'Content-Type' => 'application/json',
			],
			'cookies'     => array(),
		);
		$resp = wp_remote_post('http://192.168.1.59:8081/coupons/get-coupon-code', $args);
		$data = json_decode(wp_remote_retrieve_body($resp));

		print_r($data);
		// $coupon_code = $data->couponCode;
		// $discount_percentage = $data->discount;
		// $data->couponCode
		$coupon = new WC_Coupon();

		$coupon_code = "7884975190"; //$data->couponCode;
		$coupon->set_code($coupon_code); // Coupon code
		$coupon->set_amount(50);  //$coupon->set_amount($data->discount); // Discount amount
		
		$coupon->set_usage_limit(1);

		$coupon_codes = $this->get_available_coupon_codes(); // Initializing
		
	
		print("in debug 1");
		print($coupon_code);
		print_r($coupon_codes);
				echo "<pre>";
		print_r(WC()->cart->get_applied_coupons());
				//print_r($coupon);
				echo "</pre>";
				
		if($coupon_code!=null){

			if (empty($coupon_codes)) {
				$coupon->save();
				
					$this->display_message($coupon_code);
				
				
				// echo "Array is empty and Coupon is added to Database";
			} else {

				// echo $code;
				
				if (!in_array($coupon_code, $coupon_codes)) {
					$coupon->save();
					
						 $this->display_message($coupon_code);
					
					
					// echo "Coupon is added to Database";
				} else {
					// $applied_Coupons = WC()->cart->get_applied_coupons();

					// if (!in_array($coupon_code, $applied_Coupons)) {
					// 	$this->display_message($coupon_code);
					// }
					$this->display_message($coupon_code);							
					// echo "Already Coupon is in Database";
				}
			}
		}
	}




	function display_message($coupon_code)
	{
		if (is_admin() && !defined('DOING_AJAX')) {
			return;
		}
		//  $coupon_code = 'A3ssdGGG';
		$btn = '<button type="submit" class="button-cust" name="coupon" onclick="addDiscount() "> Apply Coupon</button>';

		$carttotal = WC()->cart->get_subtotal();
		if (is_cart()) {
			if ($carttotal < 100) {

				$applied_Coupons = WC()->cart->get_applied_coupons();
				print("debug 12.. ");

					if (in_array($coupon_code, $applied_Coupons)) {
						print("debug 13.. ");
						WC()->cart->remove_coupon( $coupon_code );
					}

				$remaining = 100 - $carttotal;
				// $vr = '<div> Hey buddy if you buy item for $ ' . $remaining . ' more ,you have the coupon </div>';
				// wc_add_notice(__($vr), "notice");
				// wc_clear_notices();
				$notice = sprintf("Add %s worth more products to avail the coupon", wc_price($remaining));
				wc_print_notice($notice, 'notice');
				// do_action( 'woocommerce_cart_actions' ); 

			}
			else {
				// WC()->cart->apply_coupon($coupon_code);
				// $vr = '<div> your coupon is applied' . '</div>';
				// $notice = sprintf("" . $vr);
				// wc_print_notice($notice, 'notice');
				
					// $this->addDiscount($coupon_code);
				// 	wc_add_notice(__($vr), "notice");
				// $is_discount_eligible = true;
				// return $is_discount_eligible;


				$applied_Coupons = WC()->cart->get_applied_coupons();

					if (!in_array($coupon_code, $applied_Coupons)) {
						$this->addDiscount($coupon_code);
					}
				

				// 	// do_action( 'woocommerce_cart_actions' ); 
			}
		}
	}

	function addDiscount($coupon_code)
	{

		print("in addDiscount ..");
		// global $woocommerce;
		if (is_admin() && !defined('DOING_AJAX')) {
			return;
		}
		// if (WC()->cart->has_discount($coupon_code)) return;
		WC()->cart->apply_coupon($coupon_code);
	//	wc_print_notices();
		$vr = '<div> your coupon is applied' . '</div>';
		$notice = sprintf("" . $vr);
		wc_print_notice($notice, 'notice');
	}


	function discountLabel($label, $coupon)
	{
		if ($coupon->code == $this->coupon_code) {
			return __('Custom discount', 'txtdomain');
		}
		return $label;
	}

	function discountHtml($coupon_html, $coupon, $discount_amount_html)
	{
		if ($coupon->code == $this->coupon_code) {
			return $discount_amount_html;
		}
		return $coupon_html;
	}
}
new customer_cart_details();
