<?php

/**
 * Plugin Name: Phaseone
 * Plugin URI:  Plugin URL Link
 * Author:      Plugin Author Name
 * Author URI:  Plugin Author Link
 * Description: This plugin does 
 * Version:     0.1.0
 * License:     GPL-2.0+
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: Phaseone for woocommerce
 */

class WC_Phaseone_core
{

    //The single instance class
    public static $instance = null;

    public function __construct()
    {
        $this->add_hooks();
    }
    public function add_hooks()
    {
        add_action('admin_menu', array($this, 'admin_menu_phaseone'));
        // add_action('admin_menu', array($this, 'CallAPI'));
        add_action('admin_enqueue_scripts', array($this, 'load_styles_scripts'));
        
    }
    public function admin_menu_phaseone()
    {
        // echo plugins_url('assets/css/animate.css', __FILE__);
        add_menu_page('PhaseOne', 'Phase Menu', 'manage_options', 'admin_menu_icon', array($this, 'admin_menu_phaseone_main'), 'dashicons-cart', 1);
    }
    // Method: POST, PUT, GET etc
                        // Data: array("param" => "value") ==> index.php?param=value
                        // $url ="https://pivotrics-my.sharepoint.com/personal/william_baretto_pivotrics_com/_layouts/15/onedrive.aspx?id=%2Fpersonal%2Fwilliam%5Fbaretto%5Fpivotrics%5Fcom%2FDocuments%2FMicrosoft%20Teams%20Chat%20Files%2FCoupons%2Epostman%5Fcollection%2Ejson&parent=%2Fpersonal%2Fwilliam%5Fbaretto%5Fpivotrics%5Fcom%2FDocuments%2FMicrosoft%20Teams%20Chat%20Files&ga=1";
                        // function CallAPI( $url, $data = false)
                        // {
                        //     $curl = curl_init();

                        //     // switch ($method) {
                        //     //     case "POST":
                        //     //         curl_setopt($curl, CURLOPT_POST, 1);

                        //     //         if ($data)
                        //     //             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                        //     //         break;
                        //     //     case "PUT":
                        //     //         curl_setopt($curl, CURLOPT_PUT, 1);
                        //     //         break;
                        //     //     default:
                        //     //         if ($data)
                        //     //             $url = sprintf("%s?%s", $url, http_build_query($data));
                        //     // }

                        //     // Optional Authentication:
                        //     // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                        //     // curl_setopt($curl, CURLOPT_USERPWD, "username:password");

                        //     curl_setopt($curl, CURLOPT_URL, $url);
                        //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                        //     $result = curl_exec($curl);

                        //     curl_close($curl);
                            
                        //     return $result;
                        // }

    public function admin_menu_phaseone_main()
    {

        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // Get the active tab from the $_GET param
        $default_tab = null;
        $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
?>
        <!-- Our admin page content should all be inside .wrap -->
        <div class="wrap">
            <!-- Print the page title -->
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <!-- Here are our tabs -->
            <nav class="nav-tab-wrapper">
                <a href="?page=admin_menu_icon" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>">Default Tab</a>
                <a href="?page=admin_menu_icon&tab=Customer_Cart" class="nav-tab <?php if ($tab === 'Customer_Cart') : ?>nav-tab-active<?php endif; ?>">Customer Cart</a>
                <a href="?page=admin_menu_icon&tab=Order_details" class="nav-tab <?php if ($tab === 'Order_details') : ?>nav-tab-active<?php endif; ?>">Order Details</a>
            </nav>

            <div class="tab-content">
                <?php switch ($tab):
                    case 'Customer_Cart':
                        require_once(plugin_dir_path(__FILE__) . 'customer_cart.php');
                        break;
                    case 'Order_details':
                        $dat = require_once('order_details.php');
                        echo 'Tools';
                        break;

                    default:
                  
                        echo "default tab";
                        //require_once('landing-page.html');
                        break;
                endswitch; ?>
            </div>
        </div>
<?php


    }
    public function load_styles_scripts()
    {
        // wp_enqueue_style('animate', plugins_url('assets/css/animate.css', __FILE__));
        // wp_enqueue_style('bootstrap.min', plugins_url('assets/css/bootstrap.min.css', __FILE__));
        // wp_enqueue_style('font-awesome.min', plugins_url('assets/css/font-awesome.min.css', __FILE__));
        wp_enqueue_style('main', plugins_url('assets/css/main.css', __FILE__));
        // wp_enqueue_style('checkout', trailingslashit(WC_ABSPATH).'assets/js/frontend/checkout.js');
        // wp_enqueue_style('checkout-min', trailingslashit(WC_ABSPATH).'assets/js/frontend/checkout.min.js');
        // wp_enqueue_style('checkout-min', trailingslashit(WC_ABSPATH).'assets/js/frontend/checkout.min.js');
        // wp_enqueue_style('perfect-scrollbar', plugins_url('assets/css/perfect-scrollbar.css', __FILE__));
        // wp_enqueue_style('select2.min', plugins_url('assets/css/select2.min.css', __FILE__));
        // wp_enqueue_style('util', plugins_url('assets/css/util.css', __FILE__));
    }

    /**
     * Get the instance of the class
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
WC_Phaseone_core::instance();
