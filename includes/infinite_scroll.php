<?php

add_shortcode('infinite-products', 'shortcode'  );
function shortcode(){
    return '<div class="product-list-container"></div>';
    //simple shortcode to add container of post list
}

function custom_shortcode_scripts() {
    global $post;
    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'infinite-products') ) {
        //load script if shortcode is present

        global $wp_query;

        //values for javascript object pass with wp localize script
        $args = array(
            'nonce'       => wp_create_nonce('load-more-nonce'),
            'url'         => admin_url('admin-ajax.php'),
            'query'       => $wp_query->query,
            'maxpages'    => $wp_query->max_num_pages
        );
        $url = plugin_dir_url( __FILE__ );
        wp_enqueue_style('font-awesome', $url.'../css/font-awesome.min.css',__FILE__,array());
        wp_enqueue_style('infinite-scroll', $url.'../css/infinite-scroll.css',__FILE__,array());
        wp_enqueue_script('load-more', $url. '../js/load-more.js', array( 'jquery' ), '1.0', true);
        wp_localize_script('load-more', 'loadmore', $args);
    }
}
add_action( 'wp_enqueue_scripts', 'custom_shortcode_scripts');
add_action( 'wp_ajax_load_more_ajax', 'load_more_ajax');
add_action( 'wp_ajax_nopriv_load_more_ajax', 'load_more_ajax');

function load_more_ajax(){
    //ajax function to load more post
    check_ajax_referer('load-more-nonce', 'nonce');

    ob_start();

    $query = new WP_Query([
        'post_type'         => 'products',
        'post_status'       => 'publish',
        'posts_per_page'    => 5, // custom option, we can use the option registered in wordpress global options too
        'paged'             => $_POST['page']
    ]);

    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
            ?>
                <div class="col-ms-6 col-sm-6 col-md-6 shop-item">
                    <div class="card card-product">
                        <div class="card-image">
                            <a href="<?php echo get_permalink() ?>" title="<?php echo get_the_title()?>">
                                <?php echo the_post_thumbnail( array(150, 150) );?>
                            </a>
                            <div class="ripple-container"></div>
                        </div>
                        <div class="content">
                            
                            <h4 class="card-title">
                                <a class="shop-item-title-link" href="<?php echo get_permalink() ?>" title="<?php echo get_the_title()?>">
                                    <?php echo get_the_title()?>
                                </a>
                            </h4>

                            <div class="card-description">
                                    <p><b>Brand: </b><?php echo get_the_title(get_post_meta(get_the_ID(),'brand',true)) ?></p>
                                    <?php 
                                        $rating_array = product_ratings();
                                        $rating_output = $rating_array[get_post_meta(get_the_ID(),'rating',true)]['output'];
                                    ?>
                                    <p class="product-rate"><b>Rate: </b><?php echo $rating_output?></p>
                            </div>

                            <div class="footer">
                                <a class="btn btn-info btn-sm" href="<?php echo get_permalink()?>">Product Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
             
            <?php
        endwhile;
        wp_reset_postdata();
    }

    $data = ob_get_clean();

    wp_send_json_success($data);
}