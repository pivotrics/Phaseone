<?php

if (!class_exists('customer_cart')) {
    class customer_cart
    {

        function __construct()
        {
             $this->Customer_cart_details();
            // $data=[
            //     "storeId" => "MANSZZA123",
            //     "store_url" => "woo45.local",
            //     "phoneNumber" => 989834344,
            //     "orderDetails"=> [
            //         "items" => 
            //         [
            //             [   
            //                 "itemName" => "Dress",
            //                 "quantity"=> "sss2",
            //                 "price" => 40
            //             ],
            //             [
            //                 "itemName"=> "Charger",
            //                 "quantity"=> 1,
            //                 "price"=> 20
            //             ]
            //         ]
            //     ]
            // ];
            // $body = array(
            //     'storeId'    => 'Asdfg123',
            //     'store_url'   => 'woo4.local',
            //     'phoneNumber' => '9800123444',
            // );
            // $args = array(
            //     'body'        => $body,
            //     'timeout'     => '5',
            //     'redirection' => '5',
            //     'httpversion' => '1.0',
            //     'blocking'    => true,
            //     'headers'     => array(),
            //     'cookies'     => array(),
            // );
            // $vart = http_build_query($data);
            //  $this->CallAPI("http://10.1.2.12:8081/coupons/order-details",$args);
        }
        // $data="storeId=EA5758ASD8 & phoneNumber=9898099809";
        function CallAPI($url, $vart = true)
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $vart);
            $result = curl_exec($curl);

            // print_r($data);
            curl_close($curl);

            return $result;
        }


        function Customer_cart_details()
        {   
            echo get_site_url();
            // $body = array(
            //     'storeId'    => 'Asdfg123',
            //     'store_url'   => 'woo4.local',
            //     'phoneNumber' => '9800123444',
            // );
            // $body =json_encode($body);
            // $args = array(
            //     'body'        => $body,  
            //     'timeout'     => '5',
            //     'redirection' => '5',
            //     'httpversion' => '1.0',   
            //     'blocking'    => true,
            //     'headers'     =>  [ 
            //         'accept'  => 'application/json',
            //         'Content-Type' => 'application/json',
            // ],
            //     'cookies'     => array(),
            // );
            // $response = wp_remote_post( 'http://10.1.2.12:8081/coupons/order-details', $args );
            // // $response = wp_remote_get( 'http://10.1.2.12:8081/coupons/status/check' );
            //  print_r($response) ;
            // $order = wc_get_order(  );
            // $order_id  = $order->get_id(); 
            // $order = wc_get_order($order_id);

            // Get the order ID
            // $parent_id = $order->get_parent_id(); // Get the parent order ID (for subscriptions…)

            // $user_id   = $order->get_user_id(); // Get the costumer ID
            // $user      = $order->get_user(); // Get the WP_User object

            // $order_status  = $order->get_status(); // Get the order status (see the conditional method has_status() below)
            // $currency      = $order->get_currency(); // Get the currency used  
            // $payment_method = $order->get_payment_method(); // Get the payment method ID
            // $payment_title = $order->get_payment_method_title(); // Get the payment method title
            // $date_created  = $order->get_date_created(); // Get date created (WC_DateTime object)
            // $date_modified = $order->get_date_modified(); // Get date modified (WC_DateTime object)

            // $billing_country = $order->get_billing_country();
            // echo $order_id;
?>
            </br>
            <table>
                <thead>
                    <tr class="table100-head">

                        <th class="column1">Name</th>
                        <th class="column1">Email ID</th>
                        <th class="column1">Phone No</th>
                        <th class="column1">Customer Role</th>
                        <th class="column1">Product ID</th>
                        <th class="column1">Product Name</th>
                        <th class="column1">Quantity</th>
                        <th class="column1">Subtotal</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    // $wc_plugin_path = trailingslashit(WP_PLUGIN_DIR) . 'woocommerce/woocommerce.php';
                    // require_once($wc_plugin_path);


                    wc_load_cart();
                    $wc_cart = WC()->cart;
                    foreach ($wc_cart->get_cart() as $cart_item_key => $cart_item) {
                        // stroe the product details here
                        $product = $cart_item['data'];
                        $customer_name = $wc_cart->get_customer()->get_display_name();
                        $customer_email = $wc_cart->get_customer()->get_email();
                        $customer_phone = $wc_cart->get_customer()->get_billing_phone();
                        $customer_role = $wc_cart->get_customer()->get_role();
                        $product_id_no = $product->get_id();
                        $product_name = $product->get_title();
                        $quantity = $cart_item['quantity'];
                        $subtotal = $wc_cart->get_product_subtotal($product, $cart_item['quantity']);
                    ?>
                        <!-- Display the details in the plugin page -->
                        <tr>
                            <td class="column1"><?php echo $customer_name; ?></td>
                            <td class="column1"><?php echo $customer_email; ?></td>
                            <td class="column1"><?php echo $customer_phone; ?></td>
                            <td class="column1"><?php echo $customer_role; ?></td>
                            <td class="column1"><?php echo $product_id_no; ?></td>
                            <td class="column1"><?php echo $product_name; ?></td>
                            <td class="column1"><?php echo $quantity; ?></td>
                            <td class="column1"><?php echo $subtotal; ?></td>
                        </tr>

                    <?php
                        // echo trailingslashit(WC_ABSPATH).'assets/js/frontend/checkout.js';
                    } ?>
                </tbody>
            </table>
            <!-- wc_get_template( 'checkout/form-billing.php', array( 'checkout' => $this ) ); -->
<?php
            // woocommerce_form_field( 'vat', array(
            //     'type'        => 'text',
            //     'required'    => true,
            //     'label'       => 'VAT',
            //     'description' => 'Please enter your VAT',
            // ));
            // wc_get_template( 'checkout/form-billing.php', array( 'checkout' => WC_Checkout() ) );
        }
    }
    $customer_cart = new customer_cart();
}
