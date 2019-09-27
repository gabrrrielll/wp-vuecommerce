<?php
/*
Plugin Name: VUE Commerce
Plugin URI: https://github.com/gabrrrielll/wp-vuecommerce
Description: This plugin will transform your wordpress site into a modern  ecommerce platform made with the latest tehnology.
Author: Sandu Gabriel
Author URI: https://github.com/gabrrrielll
Version: 1.0.1
*/

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
        'has_archive' => true,
        'register_meta_box_cb' => 'register_vue_box',
        'suports' => array( 'title', 'editor', 'thumbnail')
    );


    register_post_type( $name, $args );
};

add_action( 'init', 'wp_vue_create_custom_product' );


// Add metaboxes to product
function register_vue_box(){
    add_meta_box( 'price','Price', 'price_display', 'vueproduct','normal' );
    add_meta_box( 'category','Category', 'category_display', 'vueproduct','side' );
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

function category_display(){
    global $post;
    $category = get_post_meta( $post->ID, 'category', true );
   
   ?>

    <label >Select category</label>
   <input type ="select"  name="category" value = "<?php print $category; ?>"/> <br />
   <label>Add new category</label>
   <input type ="text"  name="addcategory" value = ""/>

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
        if (array_key_exists( 'addcategory', $_POST )){
            update_post_meta( $post_id, 'category', $_POST['addcategory'] );
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
