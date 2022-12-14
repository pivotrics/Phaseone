<?php
// Order details  to the server side
if (!class_exists('order_details')) {

    class order_details
    {

        public function __construct()
        {

             $this->order_status = array(
                 'completed'		=> __( 'Completed', 'Phaseone_for_woocommerce' ),
                 'cancelled'		=> __( 'Cancelled', 'Phaseone_for_woocommerce' ),
                 'failed'		=> __( 'Failed', 'Phaseone_for_woocommerce' ),
                 'refunded'		=> __( 'Refunded', 'Phaseone_for_woocommerce' ),
                 'processing'	=> __( 'Processing', 'Phaseone_for_woocommerce' ),
                 'pending'		=> __( 'Pending', 'Phaseone_for_woocommerce' ),
                 'on-hold'		=> __( 'On Hold', 'Phaseone_for_woocommerce' ),
             );
            // add_action('woocommerce_new_order', 'customer_orders', 10, 1);
            // add_action('woocommerce_order_status_completed','payment_complete');
            
            $this ->customer_orders_details();
            // $this->CallAPI("http://10.1.2.12:8081/coupons/order-details","");
        }

        /**
         * Functions
         */


        function customer_orders($order_id)
        {   
            if ( ! $order_id ) {
                return;
              }
             
              $order = wc_get_order( $order_id );
             
              $order->update_status( 'completed' );
             
            // $order = new WC_Order( $order_id );
    // $items = $order->get_items();
    // foreach ( $order->get_items() as $item_key => $item ) {
    //     $product = $item->get_product(); // the WC_Product object
    //     $product_type = $product->get_type();
    //     $product_sku = $product->get_sku();
    //     $product_price = wc_price($product->get_price());
    //     $stock_quantity = $product->get_stock_quantity();
    // }
            // if (!$order_id)
            //     return;
            // // Allow code execution only once 
            // if (!get_post_meta($order_id, '_thankyou_action_done', true)) {
            //     // Get an instance of the WC_Order object
            //     $order = wc_get_order($order_id);

            //     $order_data = $order->get_data();
            //     // Get the order key
            //     $order_key = $order->get_order_key();
            //     // Get the order number
            //     $order_key = $order->get_order_number();
            //     $order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
            //     $order_billing_phone = $order_data['billing']['phone'];
            //     // Loop through order items
            //     foreach ($order->get_items() as $item_id => $item) {
            //         // Get the product object
            //         $product = $item->get_product();
            //         // Get the product Id
            //         $product_id = $product->get_id();
            //         // Get the product name
            //         $product_id = $item->get_name();
            //         // Get the product quantity
            //         $product_quantity = $item->get_quantity();;
            //     }

            //     // Flag the action as done (to avoid repetitions on reload for example)
            //     $order->update_meta_data('_thankyou_action_done', true);
            //     $order->save();
            //     // sending the order details to server
                
            // }
            
        }

        

            function payment_complete($order_id)
            {
            global $items;
            $order = new WC_Order($order_id);
            // do something ...
            echo "manish";
            $body = array(
                'storeId'    => 'kkksdfg123',
                'store_url'   => 'woo4.local',
                'phoneNumber' => '1234254545',
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
            wp_remote_post('http://10.1.2.12:8081/coupons/order-details', $args);
            }



        function customer_orders_details()
        {

            global $wpdb;


            $args = array(
                'limit' => -1,
            );
            $orders = wc_get_orders($args);
            // $orders->update_status( 'completed' );
             
            $var = $today_checkin_var = $today_checkout_var = $booking_time = "";
            foreach ($orders as $id_key => $order) {

                //$order = wc_get_order( $id_value->order_id );

                //if ( $order->get_status() === 'completed' ) {
                    if( 'processing'== $order->get_status() ) {
                        $order->update_status( 'wc-completed' );
                    }
                $order_items = $order->get_items();

                $my_order_meta = get_post_custom($order->get_id());
                //  $order->get_order_key();
                //  print_r($order) ;
                $c = 0;
                foreach ($order_items as $items_key => $items_value) {
                    $var .= "<tr>
                             <td>" . $order->get_id() . "</td>
                             <td>" . $this->order_status[$order->get_status()] . "</td>
                             <td>" . $my_order_meta['_billing_first_name'][0] . " </td>
                             <td>" . $my_order_meta['_billing_email'][0] . "</td>
                             <td>" . $my_order_meta['_billing_phone'][0] . "</td>
                             <td>" . $items_value['name'] . "</td>
                             <td>" . $items_value['line_total'] . "</td>
                             <td>" . $order->get_date_created() . "</td>
                             <td><a href=\"post.php?post=" . $order->get_id() . "&action=edit\">View Order</a></td>
                             </tr>";

                    $c++;
                }
                //}
            }
?>
            <!-- <div style="float: left;">
                 <h2>
                     <strong>All Orders</strong>
                 </h2>
             </div> -->
            <div>
                </br>
                <table id="order_history" class="table100-head" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><?php _e('Order ID', 'Phaseone_for_woocommerce'); ?></th>
                            <th><?php _e('Order Status', 'Phaseone_for_woocommerce'); ?></th>
                            <th><?php _e('Customer Name', 'Phaseone_for_woocommerce'); ?></th>
                            <th><?php _e('Customer Email', 'Phaseone_for_woocommerce'); ?></th>
                            <th><?php _e('Customer Phone', 'Phaseone_for_woocommerce'); ?></th>
                            <th><?php _e('Product Name', 'Phaseone_for_woocommerce'); ?></th>
                            <th><?php _e('Amount', 'Phaseone_for_woocommerce'); ?></th>
                            <th><?php _e('Order Date', 'Phaseone_for_woocommerce'); ?></th>
                            <th><?php _e('Action', 'Phaseone_for_woocommerce'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $var; ?>
                    </tbody>
                </table>
            </div>

<?php
            //  Api connection order details
            //  function CallAPI( $url, $data = false)
            //  {
            //      $curl = curl_init();
            //      curl_setopt($curl, CURLOPT_URL, $url);
            //      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            //      $data=
            //      $result = curl_exec($curl);
            //      echo $result;
            //      curl_close($curl);

            //      return $result;
            //  }

        }
    }
}

$order_details = new order_details();
