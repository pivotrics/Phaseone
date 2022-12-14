<?php 

if (!class_exists('order_detail')) 
{
    class order_detail
    {
        public function __construct()
        {
           // print("inside connection page...");
            add_action( 'woocommerce_thankyou', [$this, 'change_order_status'] );
            add_action('woocommerce_thankyou', [$this, 'order_details_to_server'], 10, 1);
        }

        function  change_order_status( $order_id ) {  

           // print('inside change_order_status' + $order_id);
            if ( ! $order_id ) {
                return;
            }            
            $order = wc_get_order( $order_id );
            if( 'processing'== $order->get_status() ) {
                $order->update_status( 'wc-completed' );
            }
        }
        
        function order_details_to_server( $order_id ) {


       // print('inside order_details_to_server' + $order_id);

            if ( ! $order_id )
                return;

            $store_url = get_site_url();
            // Getting an instance of the order object
            $order = wc_get_order( $order_id );
            $applied_coupons = implode(" ", $order->get_coupon_codes());
            $status = $order->get_status();
            $coupon_applied = "false";
            if($status == "completed"){
                $coupon_applied = "true";
            }
            $billing_phone  = $order->get_billing_phone();
            $total = $order->get_total();
            // iterating through each order items (getting product ID and the product object) 
            $orders=array();
            foreach ( $order->get_items() as $item_id => $item ) {
                $product      = $item->get_product(); 
                $order_detail = array(
                                'productId' => $item['product_id'],
                                'productName' => $item->get_name(),
                                'quantity' => $item->get_quantity(),
                                'price' => $product->get_price(),
                );
                 array_push($orders, $order_detail);                
            }
            // print_r($order_detail);
            // print_r($orders);
            $order_array =(object)array('total' => $total,'items' =>$orders);
            $coupon_array =(object)array('couponCode' => $applied_coupons,'applied' =>$coupon_applied);
            print_r($coupon_array);
            $body = array(
                'storeId'   => $store_url,
                'storeUrl'  => $store_url,
                'phoneNumber' => $billing_phone,
                'orderStatus' => $status,
                'orderId'    => $order_id,
                'orderDetails' => $order_array,
                'couponDetails' => $coupon_array,
            );
            $body = json_encode($body);
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
            $resp = wp_remote_post('http://localhost:8081/coupons/order-details', $args);
            print_r($resp);
            // echo "-----------------";
            print_r($applied_coupons);
            // echo '<p>Order ID: '. $order_id . ' — Order Status: ' . $order->get_status() . ' — Order is paid: ' . $billing_phone . '</p>';
        }
    }
    new order_detail();
}