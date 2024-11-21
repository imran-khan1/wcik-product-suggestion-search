<?php

class WCIK_Ajax_Handler {
    public static function handle_ajax_request() {
        // Verify nonce for security
        check_ajax_referer( 'wcik_search_nonce', 'security' );

        // Get search parameters
        $search_query = isset( $_POST['search_query'] ) ? sanitize_text_field( $_POST['search_query'] ) : '';
        $category = isset( $_POST['category'] ) ? intval( $_POST['category'] ) : '';
        $tags = isset( $_POST['tags'] ) ? sanitize_text_field( $_POST['tags'] ) : '';
        $price_min = isset( $_POST['price_min'] ) ? floatval( $_POST['price_min'] ) : '';
        $price_max = isset( $_POST['price_max'] ) ? floatval( $_POST['price_max'] ) : '';
        
      

        $args = [
            'post_type'      => 'product',
            'posts_per_page' => 10,
            's'              => $search_query,
        ];

        if ( ! empty( $category ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category,
            ];
        }

        if ( ! empty( $tags ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => explode( ',', $tags ),
            ];
        }

        if ( ! empty( $price_min ) || ! empty( $price_max ) ) {
            $args['meta_query'][] = [
                'key'     => '_price',
                'value'   => [ $price_min, $price_max ],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ];
        }

        $query = new WP_Query( $args );
        $results = [];

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                global $product;

                $results[] = [
                    'id'    => get_the_ID(),
                    'name'  => get_the_title(),
                    'price' => $product->get_price_html(),
                    'link'  => get_permalink(),
                    'image' => $product->get_image(),
                ];
            }
        }
        
        wp_reset_postdata();

        wp_send_json_success( $results );
    }


    public static function fetch_suggestions() {
    // Verify nonce for security
    check_ajax_referer( 'wcik_search_nonce', 'security' );

    $search_query = isset( $_POST['search_query'] ) ? sanitize_text_field( $_POST['search_query'] ) : '';

    if ( empty( $search_query ) ) {
        wp_send_json_error( [ 'message' => 'No search query provided.' ] );
    }

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 10,
        's'              => $search_query,
    ];

    $query = new WP_Query( $args );
    $results = [];

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $results[] = [
                'id'   => get_the_ID(),
                'name' => get_the_title(),
            ];
        }
    }

    wp_reset_postdata();

    if ( empty( $results ) ) {
        wp_send_json_error( [ 'message' => 'No products found.' ] );
    }

    wp_send_json_success( $results );
}

}
