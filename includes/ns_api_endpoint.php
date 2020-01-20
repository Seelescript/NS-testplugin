<?php
function nicasource_api_endpoints(){
    add_action( 'rest_api_init', 'register_nicasource_endpoints_brands');
    add_action( 'rest_api_init', 'register_nicasource_endpoints_product_categories' );
    add_action( 'rest_api_init', 'register_nicasource_endpoints_product' );
}

/** End point for brands */
function register_nicasource_endpoints_brands(){
    register_rest_route(
        'ns-api/v1',
        '/brands/',
        array(
            'methods' => array('GET','POST'),
            'callback' => 'list_brands'
        )
    );
}
function list_brands( WP_REST_Request $request ){
    //http://dev.nicasource/wp-json/ns-api/v1/brands to list all brands
    $query = new WP_Query(array(
        'post_type' => 'brands',
        'post_status' => 'publish',
        'posts_per_page'=> -1,
        'orderby'=>'title',
        'order'=>'ASC'
    ));

    $brand_list = array();

    while ($query->have_posts()) {

        $query->the_post();
        $brand_id = get_the_ID();
        $brand_name = get_the_title();
        array_push($brand_list, array('Title' => $brand_name, 'ID' => $brand_id));
    }

    wp_reset_query();

    return $brand_list;
}
/** End point for brands */



/** End point for Product Categories custom taxonomy*/
function register_nicasource_endpoints_product_categories(){
    register_rest_route(
        'ns-api/v1',
        '/product-categories/',
        array(
            'methods' => array('GET','POST'),
            'callback' => 'list_product_categories'
        )
    );
}

function list_product_categories( WP_REST_Request $request ){
    //http://dev.nicasource/wp-json/ns-api/v1/product-categories to list all categories
    $terms = get_terms( array(
        'taxonomy' => 'product_category',
        'orderby' => 'name',
        'hide_empty' => 0,
    ) );
    return $terms;
}
/** End point for Product Categories custom taxonomy*/


/** End point for Product*/
function register_nicasource_endpoints_product(){
    register_rest_route(
        'ns-api/v1',
        '/products/',
        array(
            'methods' => array('GET','POST'),
            'callback' => 'list_products',
            'args' => array(
                'brand' => array(
                    'required' => false,
                    'validation_callback'=> function($param, $request, $key){
                        return is_string($param);
                    }
                ),
                'featured' => array(
                    'required' => false,
                    'validation_callback'=> function($param, $request, $key){
                        return is_string($param);
                    }
                ),
            )
        )
    );
}
function list_products(WP_REST_Request $request){
    //rest api for product, the url register is
    //http://dev.nicasource/wp-json/ns-api/v1/products
    //http://dev.nicasource/wp-json/ns-api/v1/products?rated for top rated
    //http://dev.nicasource/wp-json/ns-api/v1/products?featured for featured post
    //http://dev.nicasource/wp-json/ns-api/v1/products?brand=nameofbrand  // to get products by brands, if brand do not exist show error
    //http://dev.nicasource/wp-json/ns-api/v1/products?category=categoryname  // to get products by category, if category do not exist show error
    $params = $request->get_params();

    $args = array(
        'post_type' => 'products',
        'post_status' => 'publish',
        'posts_per_page'=> -1,
        'orderby'=>'title',
        'order'=>'ASC'
    );
    if(isset($params['brand'])){

        //query to get the id of brand by name
        $query = new WP_Query(
            array(
                'name' => $params['brand'],
                'post_type' => 'brands'
            )
        );
        if ($query->have_posts()) {
            //get the id of the brand
            $brand_id = $query->posts[0]->ID;
        } else {
            //if brand name do not exist, show error in the API
           return new WP_Error( 'brand_error','No brand was found with the name: '.$params['brand'],$params);
        }

        wp_reset_query();

        $args = array(
            'post_type' => 'products',
            'post_status' => 'publish',
            'posts_per_page'=> -1,
            'orderby'=>'title',
            'order'=>'ASC',
            'meta_key' => 'brand',
            'meta_value'=> $brand_id
        );
    }
    if(isset($params['featured'])){
        $args = array(
            'post_type' => 'products',
            'post_status' => 'publish',
            'posts_per_page'=> -1,
            'orderby'=>'title',
            'order'=>'ASC',
            'meta_key' => 'featured',
            'meta_value'=> 'yes'
        );
    }
    if(isset($params['rated'])){
        $args = array(
            'post_type' => 'products',
            'post_status' => 'publish',
            'posts_per_page'=> -1,
            'orderby'=>'meta_value',
            'order'=>'DESC',
            'meta_key' => 'rating'
        );
    }
    if(isset($params['category'])){
        $term = get_term_by('name', $params['category'], 'product_category');
        if($term){
            $args = array(
                'post_type' => 'products',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_category',
                        'field' => 'slug', //can be set to ID
                        'terms' => $params['category'] //if field is ID you can reference by cat/term number
                    )
                )
            );
        }else{
            //if Category name do not exist, show error in the API
            return new WP_Error( 'category_error','No Category was found with the name: '.$params['category'],$params);
        }
    }

    $query = new WP_Query($args);

    $product_list = array();

    while ($query->have_posts()) {

        $query->the_post();
        $product_id = get_the_ID();
        $product_name = get_the_title();
        $meta = get_post_meta($product_id);
        $brand = $meta['brand'];
        $rating = $meta['rating'][0];
        $categories = get_the_terms( $product_id,'product_category' );
        $content = get_the_content();

         //Nutritions Facts from custom post meta
        $serving_size = $meta['serving-size'][0];
        $calories = $meta['calories'][0];
        $calories_fat = $meta['calories-fat'][0];
        $total_fat_gr = $meta['total-fat-gr'][0];
        $total_fat_pc = $meta['total-fat-pc'][0];
        $satured_fat_gr = $meta['satured-fat-gr'][0];
        $satured_fat_pc = $meta['satured-fat-pc'][0];
        $trans_fat = $meta['trans-fat'][0];
        $cholesterol_gr = $meta['cholesterol-gr'][0];
        $cholesterol_pc = $meta['cholesterol-pc'][0];
        $sodium_gr = $meta['sodium-gr'][0];
        $sodium_pc = $meta['sodium-pc'][0];
        $carbohydrate_gr = $meta['carbohydrate-gr'][0];
        $carbohydrate_pc = $meta['carbohydrate-pc'][0];
        $fiber_gr = $meta['fiber-gr'][0];
        $fiber_pc = $meta['fiber-pc'][0];
        $sugars = $meta['sugars'][0];
        $protein = $meta['protein'][0];
        $vitamina = $meta['vitamina'][0];
        $vitaminc = $meta['vitaminc'][0];
        $calcium = $meta['calcium'][0];
        $iron = $meta['iron'][0];

        array_push($product_list, array(
            //'meta' => $meta,
            __('Title','NicaSource') => $product_name,
            __('ID','NicaSource') => $product_id,
            __('Brand','NicaSource') => get_the_title($brand[0]),
                __('Rating','NicaSource') => $rating,
                __('Category','NicaSource') => $categories[0]->name,
            __('Nutritions Facts','NicaSource') => array(
                    __('Serving Size','NicaSource') => $serving_size.'g',
                    __('Calories','NicaSource') => $calories.'g',
                    __('Calories from Fat','NicaSource') => $calories_fat,
                    __('% Daily Values * ','NicaSource')=> array(
                        __('Total Fat','NicaSource') => $total_fat_gr.'g  '. $total_fat_pc . '%',
                        __('Satured Fat','NicaSource') => $satured_fat_gr.'g  '. $satured_fat_pc . '%',
                        __('Trans Fat','NicaSource') => $trans_fat.'g',
                        __('Cholesterol','NicaSource') => $cholesterol_gr.'mg  '. $cholesterol_pc . '%',
                        __('Sodium','NicaSource') => $sodium_gr.'mg  '. $sodium_pc . '%',
                        __('Carbohydrate','NicaSource') => $carbohydrate_gr.'g  '. $carbohydrate_pc . '%',
                        __('Dietary Fiber','NicaSource') => $fiber_gr.'g  '. $fiber_pc . '%',
                        __('Sugars','NicaSource') => $sugars.'g',
                        __('Protein','NicaSource') => $protein.'g',
                        __('Vitamin A','NicaSource') => $vitamina.'%',
                        __('Vitamin C','NicaSource') => $vitaminc.'%',
                        __('Calcium','NicaSource') => $calcium.'%',
                        __('Iron','NicaSource') => $iron.'%',
                    )
                ),
                'Content'=> $content

            )
        );
    }

    wp_reset_query();

    return $product_list;
}