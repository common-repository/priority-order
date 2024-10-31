<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://syntac.co.id
 * @since      1.0.0
 *
 * @package    Priority_Order
 * @subpackage Priority_Order/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Priority_Order
 * @subpackage Priority_Order/admin
 * @author     Syntac Studio <willy.arisky@yahoo.de>
 */
class Priority_Order_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Priority_Order_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Priority_Order_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/priority-order-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register priority status 
	 * @since 1.0.0
	 */
	function register_priority_status() {
	    register_post_status( 'wc-priority-order', array(
	        'label'                     => 'Priority',
	        'public'                    => true,
	        'exclude_from_search'       => false,
	        'show_in_admin_all_list'    => true,
	        'show_in_admin_status_list' => true,
	        'label_count'               => _n_noop( 'Priority Order <span class="count">(%s)</span>', 'Priority Orders <span class="count">(%s)</span>' )
	    ) );
	}

	/**
	 * Add to list of WC Order statuses
	 * @since 1.0.0
	 */
	function add_priority_to_order_statuses( $order_statuses ) {
	    $new_order_statuses = array();

	    foreach ( $order_statuses as $key => $status ) {

	        $new_order_statuses[ $key ] = $status;
	        $new_order_statuses['wc-priority-order'] = 'Priority Order';
	    }

	    return $new_order_statuses;
	}

	/**
	 * Add priority button to action 
	 * @since 1.0.0
	 */
	function add_priority_order_actions_button( $actions, $the_order ) {
	    if ( ! $the_order->has_status( array( 'wc-priority-order' ) ) ) {
	        $actions['wc-priority-order'] = array(
	            'url'       => admin_url( 'admin.php?page=wc-priority-order&order_id=' . $the_order->id ),
	            'name'      => __( 'Priority', 'woocommerce' ),
	            'action'    => "view priority", // setting "view" for proper button CSS
	        );
	    }
	    return $actions;
	}

	/**
	 * Action add to priority order 
	 * @since 1.0.0
	 */
	function add_to_priority_action() {
		if( isset($_GET['order_id']) ) {
			$order_id = $_GET['order_id'];
			$order 	  = new WC_Order($order_id);
			$priority = date('Ymdhis');

			$update = $order->update_status( 'wc-priority-order' );
			update_post_meta( $order_id, '_priority_list', $priority );
			if($update){
				wp_redirect( admin_url('edit.php?post_status=wc-priority-order&post_type=shop_order') );
			} else {
				wp_redirect( admin_url('edit.php?post_type=shop_order') );
			}
		} else {
			wp_redirect( admin_url('edit.php?post_type=shop_order') );
		}
	}
	function hook_add_priority_action() {
		$this->plugin_screen_hook_suffix = add_submenu_page( null,
	        'Add to priority',
	        'Add to priority',
	        'manage_options',
	        'wc-priority-order',
			array( $this, 'add_to_priority_action' )
		);
	}

	/**
	 * Filter priority orders 
	 * @since 1.0.0
	 */
	function filter_priority_orders($query) {
	    global $pagenow;
	    $qv = $query->query_vars;

	    if ( $pagenow == 'edit.php' && 
	    	isset($qv['post_status']) && $qv['post_status'] == 'wc-priority-order' && 
	    	isset($qv['post_type']) && $qv['post_type'] == 'shop_order' ) 
	    	{            
	        $query->set('meta_key', '_priority_list');
	        $query->set('orderby', 'meta_value_num');
  			$query->set('order', 'DESC' );
	    }

	    return $query;
	}

}