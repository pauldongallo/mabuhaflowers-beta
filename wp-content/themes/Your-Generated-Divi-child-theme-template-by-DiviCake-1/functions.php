<?php
/**
 * Divi Cake Child Theme
 * Functions.php
 *
 * ===== NOTES ==================================================================
 * 
 * Unlike style.css, the functions.php of a child theme does not override its 
 * counterpart from the parent. Instead, it is loaded in addition to the parent's 
 * functions.php. (Specifically, it is loaded right before the parent's file.)
 * 
 * In that way, the functions.php of a child theme provides a smart, trouble-free 
 * method of modifying the functionality of a parent theme. 
 * 
 * Discover Divi Child Themes: https://divicake.com/products/category/divi-child-themes/
 * Sell Your Divi Child Themes: https://divicake.com/open/
 * 
 * =============================================================================== */
 
require get_stylesheet_directory() . '/notes/notes.php';
require get_stylesheet_directory() . '/activity-log/activity-log.php';

// function childtheme_override_body(){
//   	"<body onload='onLoad()' onresize='onResize()' >";
// }
// add_filter('body_class','childtheme_override_body');

/**
 * Register Task Logger role.
 */
require get_stylesheet_directory() . '/user-roles/roles.php';
 
function divichild_enqueue_scripts() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'divichild_enqueue_scripts' );

// 12/13/2018
/**
 * @snippet       Remove SALE badge @ Product Archives and Single Product
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=17429
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.4.5
 */
 
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 10 );

add_filter('gettext', 'translate_reply');
add_filter('ngettext', 'translate_reply');
function translate_reply($translated) {
  $translated = str_ireplace('Shipping', 'Delivery', $translated);
  return $translated;
}

add_filter('woocommerce_shipping_package_name', 'change_shipping_text_to_delivery', 20, 3 );
function change_shipping_text_to_delivery( $sprintf, $i, $package ) {
    $sprintf = sprintf( _nx( 'Delivery ', 'Delivery %d', ( $i + 1 ), 'delivery packages', 'woocommerce' ), ( $i + 1 ) );
    return $sprintf;
}

add_filter('woocommerce_order_shipping_to_display_shipped_via', 'woocommerce_remove_order_shipping_to_display_shipped_via');

function woocommerce_remove_order_shipping_to_display_shipped_via()
{
	return false;
}

// function mytheme_enqueue_style() {
// 	if(!is_user_logged_in()){
// 	    if(!is_home() ){ 
// 	    	wp_enqueue_style( 'mytheme-style', get_stylesheet_directory_uri() . '/css/default.css' ); 
// 	    }
// 	}
// }
// add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_style' );

function mytheme_enqueue_style(){
	
    wp_enqueue_style( 'parent-stylesheet', get_stylesheet_directory_uri() . '/css/dynamic_page.css', true );
    
	wp_enqueue_script( 'script', get_stylesheet_directory_uri() . '/js/divi-child-custom.js', array ( 'jquery' ), 1.1, true);
	
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_style');

add_action( 'woocommerce_thankyou', 'bbloomer_checkout_save_user_meta');
 
add_action('woocommerce_checkout_create_order', 'custom_fields_order_admin', 20, 1);
function custom_fields_order_admin($order){
	$order->update_meta_data('shop_order_mbh_assign_agent', '');
	$order->update_meta_data('shop_order_mbh_assign_agent_id', '');
	$order->update_meta_data('shop_order_mbh_dispatch_status', '');
	$order->update_meta_data('shop_order_mbh_assign_florist', '');
	$order->update_meta_data('shop_order_mbh_pay_to_florist', '');
	$order->update_meta_data('shop_order_mbh_notes_agent', '');
	$order->update_meta_data('shop_aud_currency', '');
	$order->update_meta_data('shop_currency_stat', '0');
}

add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field_delivery_instruction' );
function my_custom_checkout_field_delivery_instruction( $checkout ) {

    echo '<div id="special_delivery_instruction">';
    woocommerce_form_field( 'special_delivery_instruction', array(
        'type'          => 'textarea',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Special Delivery Instruction'),
        'placeholder'   => __(''),
        ), $checkout->get_value('special_delivery_instruction'));
    echo '</div>';
}

/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_deliver_instruction' );

function my_custom_checkout_field_deliver_instruction( $order_id ) {
    if ( ! empty( $_POST['special_delivery_instruction'] ) ) {
        update_post_meta( $order_id, '_special_delivery_instruction', sanitize_text_field( $_POST['special_delivery_instruction'] ) );
    }
}

/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta_deliver_instruction', 10, 1 );

function my_custom_checkout_field_display_admin_order_meta_deliver_instruction($order){
    echo '<p><strong>'.__('Special Instruction Delivery').':</strong> ' . get_post_meta( $order->id, '_special_delivery_instruction', true ) . '</p>';
}

/**
* Display field value on the Checkout Shipping Form
**/
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
     $fields['shipping']['shipping_phone'] = array(
        'label'     => __('Phone', 'woocommerce'),
    	'placeholder'   => _x('Phone', 'placeholder', 'woocommerce'),
    	'required'  => true,
    	'class'     => array('form-row-wide'),
    	'clear'     => true
     );

     return $fields;
}

/**
 * Display the phone number on the Woocoomerce -> Orders -> Edit Orders
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
function my_custom_checkout_field_display_admin_order_meta($order){
    echo '<p>'.get_post_meta( $order->id, '_shipping_phone', true ).'</p>';
}

/**
 * Display the phone number on the PDF
 */
add_action( 'wpo_wcpdf_after_shipping_address', 'wpo_wcpdf_shipping_phone', 10, 2 );
function wpo_wcpdf_shipping_phone ($template_type, $order) {
   if ($template_type == 'invoice') {
        ?>
            <p>Phone: <?php echo $order->get_meta('_shipping_phone'); ?> </p>
        <?php
    }
}



/**
* Display field value on the Checkout Shipping Form
**/
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields_shipping_email' );
function custom_override_checkout_fields_shipping_email( $fields ) {
     $fields['shipping']['shipping_email'] = array(
        'label'     => __('Shipping Email', 'woocommerce'),
        'placeholder'   => _x('Shipping Email', 'placeholder', 'woocommerce'),
        'required'  => true,
        'class'     => array('form-row-wide'),
        'clear'     => true
     );
     return $fields;
}

/**
 * Display the phone number on the Woocoomerce -> Orders -> Edit Orders
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_shipping_email', 10, 1 );
function my_custom_checkout_field_display_admin_order_shipping_email($order){
    echo '<p><strong>'.__('Shipping email').':</strong> ' . get_post_meta( $order->id, '_shipping_email', true ) . '</p>';
}

/**
 * Display the phone number on the PDF
 */
add_action( 'wpo_wcpdf_after_order_data', 'wpo_wcpdf_shipping_email', 10, 2 );
function wpo_wcpdf_shipping_email ($template_type, $order) {
   if ($template_type == 'invoice') {
        ?>
        <tr class="shipping-email">
            <th>Shipping Email:</th>
            <td><?php echo $order->get_meta('_shipping_email'); ?></td>
        </tr>
        <?php
    }
}






























add_filter( 'woocommerce_cart_shipping_method_full_label', 'bbloomer_remove_shipping_label', 10, 2 );
function bbloomer_remove_shipping_label($label, $method) {
	$new_label = preg_replace( '/^.+:/', '', $label );
	return $new_label;
}

/* re-arrange on the filter menu in woocommerce admin order list */
function mb_filter_views_edit_shop_order($data){ 
	
	$new_order = array(
		'all' => $data['all'],
		'wc-processing' => $data['wc-processing'],
		'wc-completed' => $data['wc-completed'],
		'wc-cancelled' => $data['wc-cancelled'],
		'wc-refunded' => refunded($data['wc-refunded']),
		'wc-failed' => wc_failed($data['wc-failed']),
		'wc-pending' => wc_pending($data['wc-pending']),
		'wc-on-hold' => $data['wc-on-hold'],
		'trash' => $data['trash']
	);
	return $new_order;
}
add_filter( 'views_edit-shop_order', 'mb_filter_views_edit_shop_order', 10, 1 );

function refunded($data){
	return ($data['wc-refunded'] != NULL ) ? $data['wc-cancelled'] : "<a href='edit.php?post_status=wc-refunded&amp;post_type=shop_order'> Refunded </a>  <span class=\"count\">(0)</span>";
}

function wc_failed($data){
	return ($data['wc-failed'] != NULL ) ? $data['wc-failed'] : "<a href='edit.php?post_status=wc-failed&amp;post_type=shop_order'> Failed </a>  <span class=\"count\">(0)</span>";
}

function wc_pending($data){
	return ($data['wc-pending'] != NULL ) ? $data['wc-pending'] : "<a href='edit.php?post_status=wc-pending&amp;post_type=shop_order'> Pending </a>  <span class=\"count\">(0)</span>";
}

/** remove the sku label from the products **/
function sv_remove_product_page_skus( $enabled ) {
    if ( ! is_admin() && is_product() ) {
        return false;
    }

    return $enabled;
}
add_filter( 'wc_product_sku_enabled', 'sv_remove_product_page_skus' );

/** add fields on the rest api **/
function my_custom_product_api_response( $product ) {
    $id = $product['product']['id'];

    // Adding your custom field:
    $product['product']['mb_product_content'] = get_post_meta( $id, '_mb_product_content', true );

    return $product;
}
add_filter( 'woocommerce_api_product_response', 'my_custom_product_api_response' );


/** custom fields **/
add_action( 'woocommerce_admin_order_data_after_order_details', 'pay_to_florist_editable_order_meta_general' );
function pay_to_florist_editable_order_meta_general( $order ){  ?>
 
		<br class="clear" />
		<h4> Florist <a href="#" class="edit_address">Edit</a></h4>
		<?php 
			/*
			 * get all the meta data values we need '_agent_pay_to_florist'
			 */ 
			$agent_pay_to_florist = get_post_meta( $order->id, '_agent_pay_to_florist', true );
			
			if( ! in_array( '_agent_pay_to_florist', get_post_custom_keys( $order->id ) ) ) {
				update_post_meta( $order->id, '_agent_pay_to_florist', wc_clean( $agent_pay_to_florist ) );
			}
			
		?>
		<div class="address">			
			<p><strong> Pay to florist:</strong> <?php echo $agent_pay_to_florist ?></p>
		</div>
		<div class="edit_address"><?php
			woocommerce_wp_text_input( array(
				'id' => 'agent_pay_to_florist',
				'label' => 'Pay to florist:',
				'value' => $agent_pay_to_florist,
				'wrapper_class' => 'form-field-wide'
			) );
		?></div>
 
 
<?php }
 
add_action( 'woocommerce_process_shop_order_meta', 'pay_to_florist_save_general_details' );
function pay_to_florist_save_general_details( $ord_id ){
	update_post_meta( $ord_id, '_agent_pay_to_florist', wc_clean( $_POST[ 'agent_pay_to_florist' ] ) );
	// wc_clean() and wc_sanitize_textarea() are WooCommerce sanitization functions
}

add_action( 'woocommerce_admin_order_data_after_order_details', 'notes_agent_editable_order_meta_general' );
function notes_agent_editable_order_meta_general( $order ){  ?>
 
		<br class="clear" />
		<h4> Florist <a href="#" class="edit_address">Edit</a></h4>
		<?php 
			/*
			 * get all the meta data values we need '_agent_pay_to_florist'
			 */ 
			$notes_agent = get_post_meta( $order->id, '_notes_agent', true );
			
			if( ! in_array( '_notes_agent', get_post_custom_keys( $order->id ) ) ) {
				update_post_meta( $order->id, '_notes_agent', wc_clean( $notes_agent ) );
			}
			
		?>
		<div class="address">			
			<p><strong> Notes :</strong> <?php echo $notes_agent ?></p>
		</div>
		<div class="edit_address"><?php
			woocommerce_wp_textarea_input( array(
				'id' => 'notes_agent',
				'label' => 'Pay to florist:',
				'value' => $notes_agent,
				'wrapper_class' => 'form-field-wide'
			) );
		?></div>
 
 
<?php }
 
add_action( 'woocommerce_process_shop_order_meta', 'notes_agent_save_general_details' );
function notes_agent_save_general_details( $ord_id ){
	update_post_meta( $ord_id, '_notes_agent', wc_clean( $_POST[ 'notes_agent' ] ) );
	// wc_clean() and wc_sanitize_textarea() are WooCommerce sanitization functions
}

add_filter( 'rest_user_query', 'prefix_remove_has_published_posts_from_wp_api_user_query', 10, 2 );
/**
 * Removes `has_published_posts` from the query args so even users who have not
 * published content are returned by the request.
 *
 * @see https://developer.wordpress.org/reference/classes/wp_user_query/
 *
 * @param array           $prepared_args Array of arguments for WP_User_Query.
 * @param WP_REST_Request $request       The current request.
 *
 * @return array
 */
function prefix_remove_has_published_posts_from_wp_api_user_query( $prepared_args, $request ) {
	unset( $prepared_args['has_published_posts'] );
	return $prepared_args;
}

function get_user_roles($object, $field_name, $request) {
  return get_userdata($object['id'])->roles;
}

add_action('rest_api_init', function() {
  register_rest_field('user', 'roles', array(
    'get_callback' => 'get_user_roles',
    'update_callback' => null,
    'schema' => array(
      'type' => 'array'
    )
  ));
});

add_action('rest_api_init', 'country_philippines');
function country_philippines(){
	register_rest_route( 'wp/v2/wbsvii' , '/province/', array(
		'methods' => 'GET',
		 'callback' => 'get_rest_phili_province',
	));
}

function get_rest_phili_province() {
	global $wpdb;
	$customers = $wpdb->get_results("SELECT * FROM refprovince ORDER BY provDesc ASC"); 
	 if (empty($customers)) {
   		return new WP_Error( 'empty_category', 'there is no post in this category', array('status' => 404) );
    }
    $response = new WP_REST_Response($customers);
    $response->set_status(200);
    return $response;
}

add_action('rest_api_init', 'city_philippines');
function city_philippines(){
	register_rest_route( 'wp/v2/wbsvii', 'city/(?P<id>\d+)',array(
        'methods'  => 'GET',
        'callback' => 'get_province_city'
    ));
}

function get_province_city($request) {
	global $wpdb;

	$prove_code = $request['id'];
	$query = "SELECT * FROM refcitymun WHERE provCode = %d";
	$prepared_query = $wpdb->prepare( $query, $prove_code );
	$city = $wpdb->get_results( $prepared_query );
    if (empty($city)) {
   		return new WP_Error( 'empty_category', 'there is no post in this category', array('status' => 404) );
    }
    $response = new WP_REST_Response($city);
    $response->set_status(200);
    return $response;
}


add_action('rest_api_init', 'city_barangay_philippines');
function city_barangay_philippines(){
	register_rest_route( 'wp/v2/wbsvii', 'barangay/(?P<id>\d+)',array(
        'methods'  => 'GET',
        'callback' => 'get_province_city_barangay'
    ));
}

function get_province_city_barangay($request) {
	global $wpdb;

	$prove_code = $request['id'];
	$query = "SELECT * FROM refbrgy WHERE citymunCode = %d";
	$prepared_query = $wpdb->prepare( $query, $prove_code );
	$city = $wpdb->get_results( $prepared_query );
    if (empty($city)) {
   		return new WP_Error( 'empty_category', 'there is no post in this category', array('status' => 404) );
    }
    $response = new WP_REST_Response($city);
    $response->set_status(200);
    return $response;
}

add_action('rest_api_init', 'get_all_prov_mun_city');
function get_all_prov_mun_city(){
	register_rest_route( 'wp/v2/wbsvii' , '/city/', array(
		'methods' => 'GET',
		 'callback' => 'get_rest_all_prov_mun_city',
	));
}

function get_rest_all_prov_mun_city() {
	global $wpdb;
	$customers = $wpdb->get_results("SELECT * FROM refcitymun"); 
	 if (empty($customers)) {
   		return new WP_Error( 'empty_category', 'there is no post in this category', array('status' => 404) );
    }
    $response = new WP_REST_Response($customers);
    $response->set_status(200);
    return $response;
}

/**
 * Add facebook fields checkout
 */
add_filter('woocommerce_checkout_fields', 'custom_woocommerce_billing_fields_facebook');
function custom_woocommerce_billing_fields_facebook($fields)
{
    $fields['billing']['billing_facebook'] = array(
        'label' => __('Facebook', 'woocommerce'), // Add custom field label
        'placeholder' => _x('', 'placeholder', 'woocommerce'), // Add custom field placeholder
        'required' => false, // if field is required or not
        'clear' => false, // add clear or not
        'type' => 'text', // add field type
        'class'         => array('my-field-class form-row-wide'),
    );
    return $fields;
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_billing_facebook' );
function custom_checkout_field_billing_facebook( $order_id ) {
    if ( ! empty( $_POST['billing_facebook'] ) ) {
        update_post_meta( $order_id, '_billing_facebook', sanitize_text_field( $_POST['billing_facebook'] ) );
    }
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_checkout_field_display_admin_order_facebook', 10, 1 );
function custom_checkout_field_display_admin_order_facebook($order){
    echo '<p><strong>'.__('Facebook').':</strong> ' . get_post_meta( $order->id, '_billing_facebook', true ) . '</p>';
}


/**
 * Add wechat fields checkout
 */
add_filter('woocommerce_checkout_fields', 'custom_woocommerce_billing_fields_wechat');
function custom_woocommerce_billing_fields_wechat($fields)
{
    $fields['billing']['billing_wechat'] = array(
        'label' => __('WeChat', 'woocommerce'), // Add custom field label
        'placeholder' => _x('', 'placeholder', 'woocommerce'), // Add custom field placeholder
        'required' => false, // if field is required or not
        'clear' => false, // add clear or not
        'type' => 'text', // add field type
        'class'         => array('my-field-class form-row-wide'),
    );
    return $fields;
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_billing_wechat' );
function custom_checkout_field_billing_wechat( $order_id ) {
    if ( ! empty( $_POST['billing_wechat'] ) ) {
        update_post_meta( $order_id, '_billing_wechat', sanitize_text_field( $_POST['billing_wechat'] ) );
    }
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_checkout_field_display_admin_order_wechat', 10, 1 );
function custom_checkout_field_display_admin_order_wechat($order){
    echo '<p><strong>'.__('WeChat').':</strong> ' . get_post_meta( $order->id, '_billing_wechat', true ) . '</p>';
}

/**
 * Add wechatApp checkout
 */
add_filter('woocommerce_checkout_fields', 'custom_woocommerce_billing_fields_whatsapp');
function custom_woocommerce_billing_fields_whatsapp($fields)
{
    $fields['billing']['billing_whatsapp'] = array(
        'label' => __('WhatsApp', 'woocommerce'), // Add custom field label
        'placeholder' => _x('', 'placeholder', 'woocommerce'), // Add custom field placeholder
        'required' => false, // if field is required or not
        'clear' => false, // add clear or not
        'type' => 'text', // add field type
        'class'         => array('my-field-class form-row-wide'),
    );
    return $fields;
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_billing_whatsapp' );
function custom_checkout_field_billing_whatsapp( $order_id ) {
    if ( ! empty( $_POST['billing_whatsapp'] ) ) {
        update_post_meta( $order_id, '_billing_whatsapp', sanitize_text_field( $_POST['billing_whatsapp'] ) );
    }
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_checkout_field_display_admin_order_whatsapp', 10, 1 );
function custom_checkout_field_display_admin_order_whatsapp($order){
    echo '<p><strong>'.__('WhatsApp').':</strong> ' . get_post_meta( $order->id, '_billing_whatsapp', true ) . '</p>';
}


/**
 * Add skype fields checkout
 */
add_filter('woocommerce_checkout_fields', 'custom_woocommerce_billing_fields_skype');
function custom_woocommerce_billing_fields_skype($fields)
{
    $fields['billing']['billing_skype'] = array(
        'label' => __('Skype', 'woocommerce'), // Add custom field label
        'placeholder' => _x('', 'placeholder', 'woocommerce'), // Add custom field placeholder
        'required' => false, // if field is required or not
        'clear' => false, // add clear or not
        'type' => 'text', // add field type
        'class'         => array('my-field-class form-row-wide'),
    );
    return $fields;
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_billing_skype' );
function custom_checkout_field_billing_skype( $order_id ) {
    if ( ! empty( $_POST['billing_skype'] ) ) {
        update_post_meta( $order_id, '_billing_skype', sanitize_text_field( $_POST['billing_skype'] ) );
    }
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_checkout_field_display_admin_order_skype', 10, 1 );
function custom_checkout_field_display_admin_order_skype($order){
    echo '<p><strong>'.__('Skype').':</strong> ' . get_post_meta( $order->id, '_billing_skype', true ) . '</p>';
}


/**
 * Add Viber fields checkout
 */
add_filter('woocommerce_checkout_fields', 'custom_woocommerce_billing_fields_viber');
function custom_woocommerce_billing_fields_viber($fields)
{
    $fields['billing']['billing_viber'] = array(
        'label' => __('Viber', 'woocommerce'), // Add custom field label
        'placeholder' => _x('', 'placeholder', 'woocommerce'), // Add custom field placeholder
        'required' => false, // if field is required or not
        'clear' => false, // add clear or not
        'type' => 'text', // add field type
        'class'         => array('my-field-class form-row-wide'),
    );
    return $fields;
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_billing_viber' );
function custom_checkout_field_billing_viber( $order_id ) {
    if ( ! empty( $_POST['billing_viber'] ) ) {
        update_post_meta( $order_id, '_billing_viber', sanitize_text_field( $_POST['billing_viber'] ) );
    }
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_checkout_field_display_admin_order_viber', 10, 1 );
function custom_checkout_field_display_admin_order_viber($order){
    echo '<p><strong>'.__('Viber').':</strong> ' . get_post_meta( $order->id, '_billing_viber', true ) . '</p>';
}



/**
 * facebook fields shipping checkout
 */
add_filter('woocommerce_after_order_notes', 'custom_woocommerce_shipping_fields_facebook');
function custom_woocommerce_shipping_fields_facebook($checkout)
{
   echo '<div id="shipping_facebook_im">';
    woocommerce_form_field( 'shipping_facebook_im', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Facebook'),
        'placeholder'   => __(''),
        ), $checkout->get_value('shipping_facebook_im'));
    echo '</div>';
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_shipping_facebook' );
function custom_checkout_field_shipping_facebook( $order_id ) {
    if ( ! empty( $_POST['shipping_facebook_im'] ) ) {
        update_post_meta( $order_id, '_shipping_facebook_im', sanitize_text_field( $_POST['shipping_facebook_im'] ) );
    }
}
/**
 * facebook fields shipping checkout order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'custom_checkout_field_display_admin_order_shipping_facebook', 10, 1 );
function custom_checkout_field_display_admin_order_shipping_facebook($order){
    echo '<p><strong>'.__('Facebook').':</strong> ' . get_post_meta( $order->id, '_shipping_facebook_im', true ) . '</p>';
}

/**
 * wechat fields shipping checkout
 */
add_filter('woocommerce_after_order_notes', 'custom_woocommerce_shipping_fields_wechat');
function custom_woocommerce_shipping_fields_wechat($checkout)
{
   echo '<div id="shipping_wechat_im">';
    woocommerce_form_field( 'shipping_wechat_im', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('WeChat'),
        'placeholder'   => __(''),
        ), $checkout->get_value('shipping_wechat_im'));
    echo '</div>';
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_shipping_wechat' );
function custom_checkout_field_shipping_wechat( $order_id ) {
    if ( ! empty( $_POST['shipping_wechat_im'] ) ) {
        update_post_meta( $order_id, '_shipping_wechat_im', sanitize_text_field( $_POST['shipping_wechat_im'] ) );
    }
}
/**
 *  wechat fields shipping order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'custom_checkout_field_display_admin_order_shipping_wechat', 10, 1 );
function custom_checkout_field_display_admin_order_shipping_wechat($order){
    echo '<p><strong>'.__('WeChat').':</strong> ' . get_post_meta( $order->id, '_shipping_wechat_im', true ) . '</p>';
}

/**
 * WhatsApp fields shipping checkout
 */
add_filter('woocommerce_after_order_notes', 'custom_woocommerce_shipping_fields_whatsapp');
function custom_woocommerce_shipping_fields_whatsapp($checkout)
{
    echo '<div id="shipping_whatsapp_im">';
    woocommerce_form_field( 'shipping_whatsapp_im', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('WhatsApp'),
        'placeholder'   => __(''),
        ), $checkout->get_value('shipping_whatsapp_im'));
    echo '</div>';
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_shipping_whatsapp' );
function custom_checkout_field_shipping_whatsapp( $order_id ) {
    if ( ! empty( $_POST['shipping_whatsapp_im'] ) ) {
        update_post_meta( $order_id, '_shipping_whatsapp_im', sanitize_text_field( $_POST['shipping_whatsapp_im'] ) );
    }
}
/**
 * whatsApp fields shipping order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'custom_checkout_field_display_admin_order_shipping_whatsapp', 10, 1 );
function custom_checkout_field_display_admin_order_shipping_whatsapp($order){
    echo '<p><strong>'.__('WhatsApp').':</strong> ' . get_post_meta( $order->id, '_shipping_whatsapp_im', true ) . '</p>';
}

/**
 * skype fields shipping checkout
 */
add_filter('woocommerce_after_order_notes', 'custom_woocommerce_shipping_fields_skype');
function custom_woocommerce_shipping_fields_skype($checkout)
{
   echo '<div id="shipping_skype_im">';
    woocommerce_form_field( 'shipping_skype_im', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Skype'),
        'placeholder'   => __(''),
        ), $checkout->get_value('shipping_skype_im'));
    echo '</div>';
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_shipping_skype' );
function custom_checkout_field_shipping_skype( $order_id ) {
    if ( ! empty( $_POST['shipping_skype_im'] ) ) {
        update_post_meta( $order_id, '_shipping_skype_im', sanitize_text_field( $_POST['shipping_skype_im'] ) );
    }
}
/**
 *  skype fields shipping checkout order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'custom_checkout_field_display_admin_order_shipping_skype', 10, 1 );
function custom_checkout_field_display_admin_order_shipping_skype($order){
    echo '<p><strong>'.__('Skype').':</strong> ' . get_post_meta( $order->id, '_shipping_skype_im', true ) . '</p>';
}

/**
 * viber field shipping checkout
 */
add_filter('woocommerce_after_order_notes', 'custom_woocommerce_shipping_fields_viber');
function custom_woocommerce_shipping_fields_viber($checkout)
{
     echo '<div id="shipping_viber_im">';
    woocommerce_form_field( 'shipping_viber_im', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Viber'),
        'placeholder'   => __(''),
        ), $checkout->get_value('shipping_viber_im'));
    echo '</div>';
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_shipping_viber' );
function custom_checkout_field_shipping_viber( $order_id ) {
    if ( ! empty( $_POST['shipping_viber_im'] ) ) {
        update_post_meta( $order_id, '_shipping_viber_im', sanitize_text_field( $_POST['shipping_viber_im'] ) );
    }
}
/**
 * viber field shipping order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'custom_checkout_field_display_admin_order_shipping_viber', 10, 1 );
function custom_checkout_field_display_admin_order_shipping_viber($order){
    echo '<p><strong>'.__('Viber').':</strong> ' . get_post_meta( $order->id, 'shipping_viber_im', true ) . '</p>';
}


add_action( 'rest_api_init', function () {
	register_rest_route( 'mod/v1', '/logout/', array(
		'methods'             => 'GET',
		'callback'            => 'mod_logout'
	) );
} );

function mod_logout() {
	wp_logout();
	wp_redirect('http://modtest.dev.websavii.com/');
	exit;
}

/**
 * Redirect users to custom URL based on their role after login
 *
 * @param string $redirect
 * @param object $user
 * @return string
 */
function wc_custom_user_redirect( $redirect, $user ) {
	// Get the first of all the roles assigned to the user
	$role = $user->roles[0];
	$dashboard = admin_url();
	$myaccount = get_permalink( wc_get_page_id( 'my-account' ) );
	if ( $role == 'customer' || $role == 'subscriber' ) {
		//Redirect customers and subscribers to the "My Account" page
		$redirect = $myaccount;
	} else {
		//Redirect any other role to the previous visited page or, if not available, to the home
		$redirect = wp_get_referer() ? wp_get_referer() : home_url();
	}
	return $redirect;
}
