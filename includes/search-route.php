<?php

add_action( 'rest_api_init',  'vueproduc_search' );

function vueproduc_search(){
    register_rest_route( 'vue', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'display_results'
    ) );
}

function display_results(){
     $products_search = new WP_Query( array (
        'post_type' => 'vueproduct'
    ));

    $results = array();

    while ( $products_search ->have_posts(  )) {
        $products_search -> the_post();
        array_push( $results, array( 
            'id' => get_the_ID(  ),
            'tilte' => get_the_title(), 
            'permalink' => get_the_permalink( ),
            'description' => get_the_content(),
            'lastprice' => get_post_meta( get_the_ID(  ), 'lastprice', true ),
            'price' => get_post_meta( get_the_ID(  ), 'lastprice', true ),
            'category' => get_object_term_cache( get_the_ID(  ) ,'vue_product_category' ) ,
            'tags' => get_object_term_cache( get_the_ID(  ) ,'vue_product_tag' ) ,
        ));
    }

    return $results;
 
}