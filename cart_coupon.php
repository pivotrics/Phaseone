
<?php

class AWPCustomDiscount
{	
	public $coupon_code ="example";
	public function __construct()
	{
		print("inside AWPCustomDiscount .. ");
		add_action('woocommerce_after_cart', [$this, 'display_message']);
		//  add_action('woocommerce_cart_calculate_fees', [$this, 'addDiscount']);
		// add_filter('woocommerce_cart_totals_coupon_label', [$this, 'discountLabel'], 10, 2);
		// add_filter('woocommerce_cart_totals_coupon_html', [$this, 'discountHtml'], 10, 3);
		// remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
		// add_action('woocommerce_checkout_coupon_message', [$this, 'addDiscount']);
	}
	// add discount

	// add_filter( 'woocommerce_coupons_enabled', 'bbloomer_disable_coupons_cart_page' );
	// function bbloomer_disable_coupons_cart_page() {
	// 	if ( is_cart() ) return false;
	// 	return true;
	// }
	function display_message()
	{
		if (is_admin() && !defined('DOING_AJAX')) {
			return;
		}
		//  $coupon_code = 'A3ssdGGG';
		$btn = '<button type="submit" class="button-cust" name="coupon" onclick="addDiscount() "> Apply Coupon</button>';

		$carttotal = WC()->cart->get_subtotal();
		if (is_cart()) {
			if ($carttotal < 200) {
				$remaining = 200 - $carttotal;
				// $vr = '<div> Hey buddy if you buy item for $ ' . $remaining . ' more ,you have the coupon </div>';
				// wc_add_notice(__($vr), "notice");
				// wc_clear_notices();
				$notice = sprintf("Add %s worth more products to avail the coupon", wc_price($remaining));
				wc_print_notice($notice, 'notice');
				// do_action( 'woocommerce_cart_actions' ); 

			}
			if ($carttotal > 200) {
				$vr = '<div> Now you can use the coupon just click the button:' . '</div>' . $btn;
				// 	wc_add_notice(__($vr), "notice");
				$notice = sprintf("".$vr);
				wc_print_notice($notice, 'notice');
				// 	// do_action( 'woocommerce_cart_actions' ); 
				if (WC()->cart->get_subtotal() > 100) {
					// add discount, if not added already
					if (!in_array($this->coupon_code, WC()->cart->get_applied_coupons())) {
						WC()->cart->apply_coupon($this->coupon_code);
						// wc_add_notice(__($vr), "notice");
					}
				} else {
					// remove discount if it was previously added
					WC()->cart->remove_coupon($this->coupon_code);
					// wc_add_notice(__($vr), "notice");
				}
			}
		}
	}

	function addDiscount()
	{
		// global $woocommerce;
		if (is_admin() && !defined('DOING_AJAX')) {
			return;
		}
		
		if (WC()->cart->get_subtotal() > 100) {
			// add discount, if not added already
			// if (!in_array($this->coupon_code, WC()->cart->get_applied_coupons())) {
			// 	WC()->cart->apply_coupon($this->coupon_code);
			// 	// wc_add_notice(__($vr), "notice");
			// }
			$discount_price = 5;
			WC()->cart->add_fee( 'Discounted Price', -$discount_price, true, 'standard');
		} else {
			// remove discount if it was previously added
			// WC()->cart->remove_coupon($this->coupon_code);
			// wc_add_notice(__($vr), "notice");
		}
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
// $coupon = new WC_Coupon($this->coupon_code);
new AWPCustomDiscount();
?>
