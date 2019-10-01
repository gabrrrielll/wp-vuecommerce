<?php
/*
Plugin Name: VUE Commerce
Plugin URI: https://github.com/gabrrrielll/wp-vuecommerce
Description: This plugin will transform your wordpress site into a modern  ecommerce platform made with the latest tehnology.
Author: Sandu Gabriel
Author URI: https://github.com/gabrrrielll
Version: 1.0.1
*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/search-route.php' );

function wp_vue_admin_menu_options(){
    add_menu_page('VUE Commerce', 'VUE Commerce ', 'manage_options', 'vue-commerce-admin-menu', 'admin_menu_options_display', 'dashicons-products', 56);
}
add_action('admin_menu', 'wp_vue_admin_menu_options');

function admin_menu_options_display(){
    ?>
    <div class="wrap">
    <h2>Options </h2>
    </div>
    <?php
}

// Create the product
function wp_vue_create_custom_product( ){
    $name = 'vueproduct';
    $args =  array(
        'labels'=>array(
            'name' => __('Products'),
            'singular_name' => __('Product'),
            'add_new' => __('Add new product'),
            'add_new_item' => __('Add new product'),
            'edit_item' => __('Edit product'),
            'search_items' => __('Search Products')
        ),
        'menu_position' => 57,
        'public' => true,
        'exclude_from_search' => false,
        'has_archive' => false,
        'register_meta_box_cb' => 'register_vue_box',
        'suports' => array( 'title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
    );


    register_post_type( $name, $args );
};

add_action( 'init', 'wp_vue_create_custom_product' );


// Add metaboxes to product
function register_vue_box(){
    add_meta_box( 'price','Price', 'price_display', 'vueproduct','normal' );
};

function price_display(){
    global $post;
    $lastprice = get_post_meta( $post->ID, 'lastprice', true );
    $price = get_post_meta( $post->ID, 'price', true );
   ?>

    <label >Last price</label>
   <input type ="number"  name="lastprice" value = "<?php print $lastprice; ?>"/> <br />
   <label>Actual price</label>
   <input type ="number"  name="price" value = "<?php print $price; ?>"/>

<?php
};


add_action( 'add_meta_boxes', 'register_vue_box' );


//save metaboxes
function save_metaboxes( $post_id ){
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );

    if ( $is_autosave || $is_revision ){
        return;
    }

    $post = get_post( $post_id );
    if( $post->post_type =="vueproduct"){
        if (array_key_exists( 'lastprice', $_POST )){
            update_post_meta( $post_id, 'lastprice', $_POST['lastprice'] );
        }
        if (array_key_exists( 'price', $_POST )){
            update_post_meta( $post_id, 'price', $_POST['price'] );
        }
        
    }
}
add_action( 'save_post', 'save_metaboxes' );


// Extract the products
function get_vueproducts(){
    $args = array(
        'posts_per_page' => 100,
        'post_type' => 'vueproduct'
    );
    $vue_products = get_posts( $args );
    echo"<pre>";
    print_r( $vue_products );
    echo"</pre>";
};

add_shortcode( 'get_vueproducts', 'get_vueproducts' );




/**
 * Create two taxonomies, Product_Categories and Product_Tags for the post type "book".
 *
 * @see register_post_type() for registering custom post types.
 */
 function vue_products_create_taxonomies() {
    // Add new taxonomy, make it hierarchical (like Product_categories)
    $labels = array(
        'name'              => __( 'Products Categories', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => __( 'Product Category', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Product Categories', 'textdomain' ),
        'all_items'         => __( 'All Products Categories', 'textdomain' ),
        'parent_item'       => __( 'Parent Product Category', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Product Category:', 'textdomain' ),
        'edit_item'         => __( 'Edit Product Category', 'textdomain' ),
        'update_item'       => __( 'Update Product Category', 'textdomain' ),
        'add_new_item'      => __( 'Add New Product Category', 'textdomain' ),
        'new_item_name'     => __( 'New Product Category Name', 'textdomain' ),
        'menu_name'         => __( 'Product Category', 'textdomain' ),
  
    );
 
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'product_category' ),
        'show_in_rest' => true,
    );
 
    register_taxonomy( 'vue_product_category', array( 'vueproduct' ), $args );
 
    unset( $args );
    unset( $labels );
 
    // Add new taxonomy, NOT hierarchical (like Product_tags)
    $labels = array(
        'name'                       => __( 'Product Tags', 'taxonomy general name', 'textdomain' ),
        'singular_name'              => __( 'Product Tag', 'taxonomy singular name', 'textdomain' ),
        'search_items'               => __( 'Search Product Tags', 'textdomain' ),
        'popular_items'              => __( 'Popular Products Tags', 'textdomain' ),
        'all_items'                  => __( 'All Products Tags', 'textdomain' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'Edit Product Tag', 'textdomain' ),
        'update_item'                => __( 'Update Product Tag', 'textdomain' ),
        'add_new_item'               => __( 'Add New Product Tag', 'textdomain' ),
        'new_item_name'              => __( 'New Product Tag Name', 'textdomain' ),
        'separate_items_with_commas' => __( 'Separate Products Tags with commas', 'textdomain' ),
        'add_or_remove_items'        => __( 'Add or remove Products Tags', 'textdomain' ),
        'choose_from_most_used'      => __( 'Choose from the most used Products Tags', 'textdomain' ),
        'not_found'                  => __( 'No Products Tags found.', 'textdomain' ),
        'menu_name'                  => __( 'Products Tags', 'textdomain' ),
    );
 
    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'product_tag' ),
        'show_in_rest' => true,
    );
 
    register_taxonomy( 'vue_product_tag', 'vueproduct', $args );
}
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'vue_products_create_taxonomies', 0 );


