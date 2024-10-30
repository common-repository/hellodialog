<?php
/**
 * Plugin Name: Hellodialog
 * Plugin URI: https://www.hellodialog.com
 * Description: This plugin connects Wordpress to the Hellodialog API.
 * Version: 1.7.15
 * Author: Webreact
 * Author URI: https://www.webreact.nl
 * License: GPLv2 or later
 * Text Domain: hellodialog
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define the plugins globals.
 *
 * @since   1.0.0
 */
global $post;

/**
 * Define the plugins directory paths.
 *
 * @since   1.0.0
 */
define( 'HD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'HD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HD_CONTENT_URL', content_url( __FILE__ ) );

class HelloDialogClass{

    function __construct() {

        if (! function_exists( 'curl_init' ) ) {
            esc_html_e('This plugin requires the CURL PHP extension', 'hellodialog');
            return false;
        }

        if (! function_exists( 'json_decode' ) ) {
            esc_html_e('This plugin requires the JSON PHP extension', 'hellodialog');
            return false;
        }

        if (! function_exists( 'http_build_query' )) {
            esc_html_e('This plugin requires http_build_query()', 'hellodialog');
            return false;
        }

        if (! class_exists('1.7.14\lib\KBApi') ) {
            require_once( HD_PLUGIN_PATH . '/lib/KBApi.php' );
        }

        /**
         * Define the WordPress actions.
         *
         * @since   1.0.0
         */
        add_action( 'admin_menu', 'hd_plugin_menu' );
        add_action( 'admin_init', 'hd_plugin_settingsings' );
        add_action( 'admin_enqueue_scripts', 'hd_enqueued_assets' );
        add_action( 'init', 'hd_shortcodes_init' );
        add_action( 'init', 'forms_init' );
        add_action( 'plugins_loaded', 'hd_plugin_init' );
        add_action( 'load-post.php', 'hd_post_meta_boxes_setup' );
        add_action( 'load-post-new.php', 'hd_post_meta_boxes_setup' );
        add_action( 'save_post', 'hd_meta_save', 10 , 3 );
        add_action( 'wp_ajax_saveContact', 'saveContact' );
		add_action( 'wp_ajax_nopriv_saveContact', 'saveContact' );
        add_action( 'wp_enqueue_scripts', 'enqueue_js' );

        /**
         * Define the WordPress filters.
         *
         * @since   1.0.0
         */
        add_filter( 'widget_text','do_shortcode' );

        /**
         * Check if WooCommerce is enabled and fire the WooCommerce integration hooks.
         *
         * @since   1.0.0
         */
        if ( !empty( get_option( 'wc_hook' ) ) ) {
            add_filter( 'woocommerce_checkout_fields', 'add_extra_fields',10 );
            add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta' );
            add_action( 'woocommerce_checkout_order_processed', 'save_extra_fields', 20, 2 );
            add_filter( 'default_checkout_news_check', 'checkout_news_check',10,2 );
            add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
        }

        /**
         * Load the plugins textdomain.
         *
         * @since   1.0.0
         */
        function hd_plugin_init() {
            load_plugin_textdomain( 'hellodialog', false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
        }

        /**
         * Load the required stylesheets.
         *
         * @since   1.0.0
         */
        function hd_enqueued_assets() {
            wp_register_style( 'hd_stylesheet', plugins_url('/assets/css/hellodialog.css', __FILE__));
            wp_enqueue_style( 'hd_stylesheet');
        }

        /**
         * Load the required scripts.
         *
         * @since   1.0.0
         */
        function enqueue_js() {
	        wp_enqueue_script( 'core', '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.js' , array('jquery'), '1.19.1', false );
            wp_enqueue_script( 'popper', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js' , array('jquery'), '1.16.1', false );
            wp_enqueue_script( 'bootstrap', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js' , array('jquery','popper'), '4.4.1', false );
            wp_enqueue_script( 'bootstrapmulti', plugins_url('/hellodialog/assets/js/bootstrap-multiselect.js') , array('jquery'), '1.0.1', false );
            wp_enqueue_script( 'ajax', plugins_url('/hellodialog/assets/js/ajax.js'), array('jquery'), '1.0.1', false );

            wp_register_style( 'boot_stylesheet', plugins_url('/assets/css/bootstrap.min.css', __FILE__));
            wp_register_style( 'bootmulti_stylesheet', plugins_url('/assets/css/bootstrap-multiselect.css', __FILE__));
            wp_register_style( 'frontend_styles', plugins_url('/assets/css/hellodialog_frontend.css', __FILE__));

            wp_enqueue_style( 'bootmulti_stylesheet');
            wp_enqueue_style( 'frontend_styles');
        }

        /**
         * Create the plugins WordPress menu pages.
         *
         * @since   1.0.0
         */
        function hd_plugin_menu() {
            add_menu_page( 'Hello Dialog', 'Hellodialog', 'administrator', 'hellodialog-main', '', 'dashicons-format-chat');
            add_submenu_page( 'hellodialog-main', __('Placeholders', 'hellodialog'), __('Placeholders', 'hellodialog'), 'administrator', 'hd-placeholders', 'hd_plugin_placeholders', 10);
            add_submenu_page( 'hellodialog-main', __('Settings', 'hellodialog'), __('Settings', 'hellodialog'), 'administrator', 'hd-settings', 'hd_plugin_settings', 1);
        }

        /**
         * Create the plugins placeholder settings page.
         *
         * @since   1.5.0
         */
        function hd_plugin_placeholders() {
            require_once( HD_PLUGIN_PATH . '/views/forms-settings.php' );
        }

        /**
         * Create the plugins settings page.
         *
         * @since   1.0.0
         */
        function hd_plugin_settings() {
            require_once( HD_PLUGIN_PATH . '/views/admin-settings.php' );
        }

        /**
         * Register the plugins settings.
         *
         * @since   1.0.0
         * @since   1.5.0 Added the registration of placeholder settings.
         */
        function hd_plugin_settingsings() {
            register_setting( 'hd-plugin-settings-group', 'api_key' );
            register_setting( 'hd-plugin-settings-group', 'wc_label' );
            register_setting( 'hd-plugin-settings-group', 'wc_hook' );
            register_setting( 'hd-plugin-settings-group', 'show_labels' );
            register_setting( 'hd-plugin-settings-group', 'optin_type' );
            register_setting( 'hd-plugin-settings-group', 'success_string' );
            register_setting( 'hd-plugin-settings-group', 'double_string' );

            /**
             * Determine if the API token was provsioned and if so register the placeholder settings.
             */
            $token = esc_attr( get_option('api_key'));
            if ( $token !== "" ) {

                KBApi::setToken($token);
                $kbFields = new KBApi('fields');
                $fields = $kbFields->get();
                $decodedResult = json_decode(json_encode($fields), true);

                foreach ( $decodedResult as $field ) {

                    $fieldname = str_replace(' ', '', 'placeholder_'.$field['name']);
                    register_setting('placeholder-settings-group', $fieldname);
                }
            }
        }

        /**
         * Register the shortcodes that will be used to show the form.
         *
         * @since   1.0.0
         * @return  content
         */
        function hd_shortcodes_init() {

            function hd_shortcode($atts = [], $content = null) {

                ob_start();
                require_once( HD_PLUGIN_PATH . '/shortcodes/shortcode.php' );
                $content = ob_get_clean();
                return $content;
            }

            add_shortcode('hellodialog', 'hd_shortcode');
        }

        /**
         * Write the array to Hellodialogs API.
         *
         * @param   $array
         * @since   1.0.0
         */
        function hd_write_api($array) {

            $token = esc_attr( get_option('api_key') );
            if (!empty ($token) ) {

                KBApi::setToken($token);
                $kbContacts     = new KBApi('contacts');
                $result         = $kbContacts->data($array)->post();
                $ar             = json_decode(json_encode($result), true);
                $allowed_html   = shapeSpace_allowed_html();
                $sanitized_success_string = wp_kses(get_option('success_string'), $allowed_html);
                $sanitized_double_string = wp_kses(get_option('double_string'), $allowed_html);

                foreach( $ar as $fields ) {
                    // Contact allready created, however show succes message
                    if ( $fields['code'] == "612" ){
                        if ( esc_attr( get_option('double_string') ) == "" ){
                            echo "You have been submitted for the newsletter.";
                        }
                        else {
                            echo $sanitized_double_string;
                        }
                    }
                    if ( $fields['code'] == "200" ){
                        if ( esc_attr( get_option('success_string') ) == "" ){
                            echo "You have been submitted for the newsletter.";
                        }
                        else {
                            echo $sanitized_success_string;
                        }
                    } else {
                        if (!is_dir(WP_CONTENT_DIR."/hellodialog")) {
                            // dir doesn't exist, make it
                            mkdir(WP_CONTENT_DIR."/hellodialog");
                        }
                        $file    = WP_CONTENT_DIR."/hellodialog/error.log";
                        $code    = $fields['code'];
                        $message = $fields['message'];
                        $date    = date('m/d/Y h:i:s a', time());
                        $err     = "API error: ".$date." Recieved code: ".$code." With message: ".$message;

                        add_action('admin_notices', 'admin_notice__error');

                        // Write errors to log file
                        $log = fopen($file, "a") or die("Unable to log error to file!");
                        fwrite($log, "\n". $err);
                        fclose($log);
                    }
                }
            } else {

                esc_html_e( 'Configure the API key first at the settings page to write to the API', 'hellodialog' );
            }
        }

        /**
         * Create the custom post type used to create the forms.
         *
         * @since   1.0.0
         */
        function forms_init() {
            $labels = array(
                'name'               => esc_html_x( 'Forms', 'post type general name', 'hellodialog' ),
                'singular_name'      => esc_html_x( 'Form', 'post type singular name', 'hellodialog' ),
                'menu_name'          => esc_html_x( 'Forms', 'admin menu', 'hellodialog' ),
                'name_admin_bar'     => esc_html_x( 'Form', 'add new on admin bar', 'hellodialog' ),
                'add_new'            => esc_html_x( 'Add', 'forms', 'hellodialog' ),
                'add_new_item'       => esc_html__( 'Add new form', 'hellodialog' ),
                'new_item'           => esc_html__( 'New form', 'hellodialog' ),
                'edit_item'          => esc_html__( 'Edit form', 'hellodialog' ),
                'view_item'          => esc_html__( 'View form', 'hellodialog' ),
                'all_items'          => esc_html__( 'All forms', 'hellodialog' ),
                'search_items'       => esc_html__( 'Search forms', 'hellodialog' ),
                'parent_item_colon'  => esc_html__( 'Main form:', 'hellodialog' ),
                'not_found'          => esc_html__( 'No forms found.', 'hellodialog' ),
                'not_found_in_trash' => esc_html__( 'No forms found in carbage.', 'hellodialog' )
            );

            $args = array(
                'labels'             => $labels,
                'description'        => __( 'Description.', 'hellodialog' ),
                'public'             => false,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => 'hellodialog-main',
                'query_var'          => true,
                'rewrite'            => array( 'slug' => 'form' ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array( 'title')
            );

            register_post_type( 'hellodialog_form', $args );
        }

        /**
         * Register a custom meta box.
         *
         * @since   1.0.0
         */
        function hd_post_meta_boxes_setup() {
            add_action( 'add_meta_boxes', 'hd_add_post_meta_boxes' );
        }

        /**
         * Add the custom meta box to the custom post type.
         *
         * @since   1.0.0
         */
        function hd_add_post_meta_boxes() {

            add_meta_box(
                'hd-post-class',
                esc_html__( 'Available API Fields', 'hellodialog' ),
                'hd_post_class_meta_box',
                'hellodialog_form',
                'normal',
                'default'
            );

            add_meta_box(
                'hd-shortcode-class',
                esc_html__( 'Shortcode usage', 'hellodialog' ),
                'hd_shortcode_class_meta_box',
                'hellodialog_form',
                'normal',
                'default'
            );
        }

        /**
         * Require the view for the forms meta box.
         *
         * @param   $post
         * @since   1.0.0
         */
        function hd_post_class_meta_box( $post ) {
            require_once( HD_PLUGIN_PATH . '/views/forms-fields-meta-box.php' );
        }

        /**
         * Require the view for the shortcode meta box.
         *
         * @param   $post
         * @since   1.0.0
         */
        function hd_shortcode_class_meta_box( $post ) {
            require_once( HD_PLUGIN_PATH . '/views/forms-shortcode-meta-box.php' );
        }

        /**
         * Save the metadate when the custom post type is mutated.
         *
         * @param   $post_id
         * @param   $post
         * @param   $update
         * @since   1.0.0
         * @since   1.2.0 Added form title and slogan.
         */
        function hd_meta_save($post_id, $post, $update){
            global $post;
            $cache = WP_CONTENT_DIR."/hellodialog/cachedfields.txt";
            if ( file_exists ( $cache ) ){
                unlink ( $cache ) ;
            }

            /**
             * Check the current save status of the post.
             *
             */
            $is_autosave    = wp_is_post_autosave( $post_id );
            $is_revision    = wp_is_post_revision( $post_id );
            $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], plugin_basename( __FILE__ ) ) ) ? 'true' : 'false';

            /**
             * Exit this script depending on the save status.
             */
            if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
                return;
            }

            /**
             * Get the form title and slogan and sanitize it.
             */
            if( isset ( $_POST['custom-meta-box2'] ) ) {
                $custom2   = (array) $_POST['custom-meta-box2'];
                $old_meta2 = (array) get_post_meta($post->ID, '_custom-meta-box2', true);

                $custom2 = array_map( 'sanitize_text_field', $custom2 );
                $old_meta2 = array_map( 'sanitize_text_field', $old_meta2 );

                if( ! empty ( $old_meta2 ) ) {
                    update_post_meta($post->ID, '_custom-meta-box2', $custom2);
                } else {
                    delete_post_meta($post->ID, '_custom-meta-box2');
                    add_post_meta($post->ID, '_custom-meta-box2', $custom2, true);
                }
            } else {
                if ( $update == true ) {
                    delete_post_meta($post->ID, '_custom-meta-box2');
                }
            }


            /**
             * Get the form fields and sanitize it.
             */
            // Get form fields
            if( isset ( $_POST['custom-meta-box'] ) ) {
                // Get input & sanitize after
                $custom   = (array) $_POST['custom-meta-box'];
                $old_meta = (array) get_post_meta($post->ID, '_custom-meta-box', true);

                // Sanitize input
                $custom = array_map( 'sanitize_text_field', $custom );
                $old_meta = array_map( 'sanitize_text_field', $old_meta );

                if( ! empty ( $old_meta ) ) {
                    update_post_meta($post->ID, '_custom-meta-box', $custom);
                } else {
                    delete_post_meta($post->ID, '_custom-meta-box');
                    add_post_meta($post->ID, '_custom-meta-box', $custom, true);
                }
            } else {
                if ( $update == true ) {
                    delete_post_meta($post->ID, '_custom-meta-box');
                }
            }
        }

        /**
         * Try to save the contact after form submission.
         *
         * @since   1.0.0
         */
        function saveContact(){
            if ( !class_exists('1.7.14\lib\KBApi') ) {
                require_once( HD_PLUGIN_PATH . '/lib/KBApi.php' );
		    }
            $nonce = $_POST['_wpnonce'];
            if ( ! wp_verify_nonce( $nonce, 'submit_form' ) ) {
                esc_html_e( 'Submission cancelled, nonce could not be verified. Please empty cache and try again', 'hellodialog' );
                exit; // Get out of here, the nonce is rotten!
            }
		    $array = array();

            /**
             * Retrieve the form data from the POST.
             */
		    foreach ( $_POST as $key => $value ){
			        if ( $key != "formSubmit" && $key !="action" && $key !="_wpnonce" && $key !="_wp_http_referer") {
			            if ( is_array( $value ) && array_key_exists("day", $value)){
                            $value = $value['year']."-".str_pad(($value['month']+1), 2, 0, STR_PAD_LEFT)."-".$value['day'];
                        }
			            if ( $key == "Email" ) {
			                //Validate mail address backend
                            if ( !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                esc_html_e( 'Invalid email format', 'hellodialog' );
                                exit;
                            }
                        }
                        //Sanitize
                        if ( is_array( $value ) ) {
			                foreach ( $value as $k => $v ) {
                                sanitize_text_field( $k );
                                sanitize_text_field( $v );
                            }
                        } else {
                            $key = sanitize_text_field( $key );
                            $value = sanitize_text_field( $value );
                        }

                        // API accepts lowercase keys only
		                $array[strtolower($key)] = $value;
                    }
		    }



            /**
             * Determine opt in.
             */
            if ( get_option('optin_type') == 'optin_type' ) {
                $array['_state'] = "Contact";
            }

            /**
             * Write the contact to the API.
             */
		    hd_write_api( $array ) ;
			die();
		}

		/**
         * Show the available fields backend.
         *
         * @since   1.0.0
         */
        function hd_available_fields($fields) {
            global $post;
            $custom_meta = get_post_meta($post->ID, '_custom-meta-box', true);
            $custom_meta2 = get_post_meta($post->ID, '_custom-meta-box2', true); ?>

            <table id="apifields">
            <thead><tr>
                <th><p><b><?php esc_html_e( 'Function', 'hellodialog' ); ?></b></p></th>
                <th><p><b><?php esc_html_e( 'Context', 'hellodialog' ); ?></b></p></th>
            </tr></thead>
            <tbody>
                <tr>
                    <td><p><?php esc_html_e( 'Form Title', 'hellodialog' ); ?></p></td>
                    <td><input type="text" name="custom-meta-box2[]" value="<?php echo $custom_meta2[0] ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td><p><?php esc_html_e( 'Form Subtitle', 'hellodialog' ); ?></p></td>
                    <td><input type="text" name="custom-meta-box2[]" value="<?php echo $custom_meta2[1]; ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td><p><?php esc_html_e( 'Optin language', 'hellodialog' ); ?></p></td>
                    <td><select name="custom-meta-box2[]">
                            <option value="NL" <?php if ( $custom_meta2[2] == 'NL' ) { echo 'selected="selected"' ; } ?> >Nederlands</option>
                            <option value="EN" <?php if ( $custom_meta2[2] == 'EN' ) { echo 'selected="selected"' ; } ?> >Engels</option>
                            <option value="DE" <?php if ( $custom_meta2[2] == 'DE' ) { echo 'selected="selected"' ; } ?> >Duits</option>
                            <option value="SE" <?php if ( $custom_meta2[2] == 'SE' ) { echo 'selected="selected"' ; } ?> >Zweeds</option>
                    </td></select>
                </tr>
            </tbody>
            </table>


            <table id="apifields">
                <thead><tr>
                    <th><p><b><?php esc_html_e( 'Fieldname', 'hellodialog' ); ?></b></p></th>
                    <th><p><b><?php esc_html_e( 'Data type', 'hellodialog' ); ?></b></p></th>
                    <th><p><b><?php esc_html_e( 'Req.', 'hellodialog' ); ?></b></p></th>
                    <th><p><b><?php esc_html_e( 'Use in form', 'hellodialog' ); ?></b></p></th>
                </tr></thead>
            <tbody>

            <?php foreach($fields as $field) {
                $required = esc_html__( 'No', 'hellodialog' );
                if ( $field['subscription_field_mandatory'] == 1) {
                    $required = esc_html__( 'Yes', 'hellodialog' );
                }
                    if ( $field['user_viewable'] == 1 ) {
                        if ($field['subscription_field_mandatory'] == 1 || $field['name'] == "Email") {
                            // If field is required, return checked
                            echo "<tr>";
                            echo "<td><p><label>" . $field['name'] . "</label></p></td>";
                            echo "<td><p><label>" . $field['type'] . "</label></p></td>";
                            echo "<td><p><label>Yes</label></p></td>"; ?>
                            <td><p><input disabled="disabled" type="checkbox" id="<?php echo $field['name']; ?>" name="custom-meta-box[]" value="<?php echo $field['name']; ?>"
                                    <?php if ( !is_array ( $custom_meta ) ) {
                                        $custom_meta = array ( $custom_meta ) ;
                                    }
                                    echo (in_array($field['name'], $custom_meta)) ? 'checked="checked"' : 'checked="checked"'; ?>
                                 <?php
                            echo "/></p></td>";

                            //Disabled won't POST, so hide it
                            ?><input type="hidden" id="<?php echo $field['name']; ?>" name="custom-meta-box[]" value="<?php echo $field['name']; ?>"
                            <?php if ( !is_array ( $custom_meta ) ) {
                                $custom_meta = array ( $custom_meta ) ;
                            }
                                    echo (in_array($field['name'], $custom_meta)) ? 'checked="checked"' : 'checked="checked"'; ?>
                                 <?php
                            echo "/></tr>";
                        } else {
                            // Else check if checked state was saved
                            echo "<tr>";
                            echo "<td><p><label>" . $field['name'] . "</label></p></td>";
                            echo "<td><p><label>" . $field['type'] . "</label></p></td>";
                            echo "<td><p><label>No</label></p></td>"; ?>
                            <td><p><input type="checkbox" id="<?php echo $field['name']; ?>" name="custom-meta-box[]" value="<?php echo $field['name']; ?>"
                                    <?php if ( !is_array ( $custom_meta ) ) {
                                        $custom_meta = array ( $custom_meta ) ;
                                    }
                                    echo (in_array($field['name'], $custom_meta)) ? 'checked="checked"' : ''; ?> /></p></td><?php
                            echo "</tr>";
                        }
                    }

            }
            echo "</tbody></table>";
        }

        /**
         * Add the signup checkbox to the WooCommerce checkout page.
         *
         * @since   1.0.0
         */
        function add_extra_fields($checkout_fields){
		    $wc_label   =   esc_attr( get_option('wc_label') );
		    if ( empty ( $wc_label ) ) {
		        $wc_label   =   "Sign up for newsletter";
            }
            $checkout_fields['billing']['news_check'] = array(
                'type'      =>  'checkbox',
                'default'   =>  1,
                'label'     =>  $wc_label,
            );

            return $checkout_fields;
        }

        /**
         * Update the order meta when user signed up for newsletter on checkout.
         *
         * @since   1.0.0
         */
        function custom_checkout_field_update_order_meta( $order_id ) {
            if ( ! empty( $_POST['news_check'] ) ) {
                update_post_meta( $order_id, 'news_check', sanitize_text_field( $_POST['news_check'] ) );
            }
        }

        /**
         * Save the data from WooCommerce checkout.
         *
         * @since   1.0.0
         */
        function save_extra_fields($order_id, $posted) {

            /**
             * Determine opt in.
             */
            if ( get_option('optin_type') == 'optin_type' ) {
                $opt_state = "Contact";
            }

            else {
                $opt_state = "Optin";
            }

            if (isset($posted['news_check'])) {
                $order = wc_get_order($order_id);
                update_user_meta($order->get_user_id(), 'news_check', $posted['news_check']);
                //update_post_meta($order_id, 'news_check', $posted['news_check']);
                $checked = get_post_meta( $order->id, 'news_check', true );

                if ( ! empty ( $checked ) ) {
                    $fname = $order->billing_first_name;
                    $lname = $order->billing_last_name;
                    $email = $order->billing_email;
                    $token = esc_attr(get_option('api_key'));
                    if ( ! empty ( $token ) ) {
                        KBApi::setToken($token);
                        $kbContacts = new KBApi('contacts');
                        // Log on response code
                        $result = $kbContacts->data(array(
                            'email'      => $email,
                            'voornaam'   => $fname,
                            'achternaam' => $lname,
                            '_state'     => $opt_state
                        ))->post();
                    }
                }
            }
        }

        /**
         * Check if the newsletter opt-in should be enabled on WooCommerce checkout.
         *
         * @since   1.0.0
         */
        function checkout_news_check($value,$input) {
            if ( is_user_logged_in()) {
                $current_user    = wp_get_current_user();
                $value           = get_user_meta( $current_user->ID, $input, true );
            }
            return $value;
        }

        /**
         * Display the newsletter signup status backend.
         *
         * @since   1.0.0
         */
        function my_custom_checkout_field_display_admin_order_meta($order) {
            echo '<p><strong>'.__('Newsletter Signup').':</strong> ' . get_post_meta( $order->id, 'news_check', true ) . '</p>';
        }

        /**
         * Print the error messages backend.
         *
         * @since   1.0.0
         */
        function admin_notice__error() {
			$class = 'notice notice-error';
			$message = __( 'Irks! An error has occurred in Hellodialog, please refer to your error.log located in your content/hellodialog directory.', 'hellodialog' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}

        /**
         * Print success messages backend.
         *
         * @since   1.0.0
         */
		function admin_notice__success() {
    		?>
			<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Settings saved!', 'hellodialog' ); ?></p>
    		</div>
			<?php
		}

        /**
         * Define the allowed HTML to be used in the messages.
         *
         * @since   1.0.0
         */
        function shapeSpace_allowed_html() {

            $allowed_tags = array(
                'a' => array(
                    'class' => array(),
                    'href'  => array(),
                    'rel'   => array(),
                    'title' => array(),
                ),
                'abbr' => array(
                    'title' => array(),
                ),
                'b' => array(),
                'blockquote' => array(
                    'cite'  => array(),
                ),
                'cite' => array(
                    'title' => array(),
                ),
                'code' => array(),
                'del' => array(
                    'datetime' => array(),
                    'title' => array(),
                ),
                'dd' => array(),
                'div' => array(
                    'class' => array(),
                    'title' => array(),
                    'style' => array(),
                ),
                'dl' => array(),
                'dt' => array(),
                'em' => array(),
                'h1' => array(),
                'h2' => array(),
                'h3' => array(),
                'h4' => array(),
                'h5' => array(),
                'h6' => array(),
                'i' => array(),
                'img' => array(
                    'alt'    => array(),
                    'class'  => array(),
                    'height' => array(),
                    'src'    => array(),
                    'width'  => array(),
                ),
                'li' => array(
                    'class' => array(),
                ),
                'ol' => array(
                    'class' => array(),
                ),
                'p' => array(
                    'class' => array(),
                ),
                'q' => array(
                    'cite' => array(),
                    'title' => array(),
                ),
                'span' => array(
                    'class' => array(),
                    'title' => array(),
                    'style' => array(),
                ),
                'strike' => array(),
                'strong' => array(),
                'ul' => array(
                    'class' => array(),
                ),
            );

            return $allowed_tags;
        }

        /**
         * Check Hellodialogs API status.
         *
         * @since   1.0.0
         */
        function hd_check_api_status(){
            $token = esc_attr( get_option('api_key'));
            if ( $token !== "" ) {
                KBApi::setToken($token);
                $kbFields       = new KBApi('fields');
                $fields         = $kbFields->get();
                $decodedResult  = json_decode(json_encode($fields), true);

                foreach ( $decodedResult as $field ) {
                    //var_dump($field);
                    if ( in_array("(#601) Invalid token (API-key)", $field)){
                        $apierror =  $field['message'];
                        echo $apierror;
                    }
                }
                if ( ! isset ( $apierror ) ) {
                    esc_html_e( 'API connection OK!', 'hellodialog' );
                }
            } else {
                esc_html_e( 'Configure the API key first to check the status', 'hellodialog' );
            }
        }
    }
}
new \HelloDialogClass();
