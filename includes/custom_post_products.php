<?php

function products_post_type()
{

    $labels = array(
        'name' => _x('Products', 'Post Type General Name', 'NicaSource'),
        'singular_name' => _x('Product', 'Post Type Singular Name', 'NicaSource'),
        'menu_name' => __('Products', 'NicaSource'),
        'name_admin_bar' => __('Product', 'NicaSource'),
        'archives' => __('Item Archives', 'NicaSource'),
        'parent_item_colon' => __('Parent Product:', 'NicaSource'),
        'all_items' => __('All Products', 'NicaSource'),
        'add_new_item' => __('Add New Product', 'NicaSource'),
        'add_new' => __('New Product', 'NicaSource'),
        'new_item' => __('New Item', 'NicaSource'),
        'edit_item' => __('Edit Product', 'NicaSource'),
        'update_item' => __('Update Product', 'NicaSource'),
        'view_item' => __('View Product', 'NicaSource'),
        'search_items' => __('Search products', 'NicaSource'),
        'not_found' => __('No products found', 'NicaSource'),
        'not_found_in_trash' => __('No products found in Trash', 'NicaSource'),
        'featured_image' => __('Featured Image', 'NicaSource'),
        'set_featured_image' => __('Set featured image', 'NicaSource'),
        'remove_featured_image' => __('Remove featured image', 'NicaSource'),
        'use_featured_image' => __('Use as featured image', 'NicaSource'),
        'insert_into_item' => __('Insert into item', 'NicaSource'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'NicaSource'),
        'items_list' => __('Items list', 'NicaSource'),
        'items_list_navigation' => __('Items list navigation', 'NicaSource'),
        'filter_items_list' => __('Filter items list', 'NicaSource'),
    );
    $args = array(
        'label' => __('Product', 'NicaSource'),
        'description' => __('Product information pages.', 'NicaSource'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail', 'revisions',),
        'hierarchical' => true,
        'rewrite' => array('slug' => 'brand-%brand_slug%/product-%product_slug%', 'with_front' => false),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-products',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );

    register_post_type('products', $args);
    add_rewrite_rule("^brand-([^/]+)/product-([^/]+)/([^/]+)?",'index.php?post_type=products&brand_slug=$matches[1]&product_slug=$matches[2]&products=$matches[3]','top');
    flush_rewrite_rules();
}

function my_post_type_link_filter_function($post_link, $id = 0, $leavename = FALSE) {
    //filter to replace the strings for values stored in the metadata of the post
    if (strpos('%brand_slug%', $post_link) === FALSE && strpos('%product_slug%', $post_link) === FALSE ) {
        $post = &get_post($id);
        $brand_name = get_post(get_post_meta($post->ID,'brand',true));
        $bran_slug = $brand_name->post_name;
        $name= $post->post_name;
        if(empty($bran_slug)){$bran_slug = 'default';} //use default as brand if no brand register
        $post_link = str_replace('%brand_slug%',$bran_slug, $post_link);
        $post_link = str_replace('%product_slug%',$name, $post_link);
        return $post_link;
    }
}
add_filter('post_type_link', 'my_post_type_link_filter_function', 10, 2);


add_action('init', 'prepare_product_stuff');
function prepare_product_stuff()
{
    //add metadata, scripts and css to show products.
    add_action('add_meta_boxes', 'add_product_metabox');
    add_action( 'save_post', 'ns_save_meta');
    add_action( 'wp_enqueue_scripts', 'add_custom_css' );
    add_action( 'admin_enqueue_scripts', 'add_admin_css' );
    add_filter( 'the_content', 'add_content_filter');


}

function add_product_metabox()
{
    //metaboxes - separate in 2 boxes to show clear the values of Nutrition Facts
    add_meta_box('product-details', 'Product Details', 'ns_product_meta', 'products', 'normal', 'low');
    add_meta_box('product-nutrition-facts', 'Nutrition Facts', 'ns_nutrition_facts', 'products', 'side', 'low');
}

function add_custom_css(){
    //include css for products in admin and front end
    $url = plugin_dir_url( __FILE__ );
    if( !is_admin() ){
        wp_enqueue_style('font-awesome', $url.'../css/font-awesome.min.css',__FILE__,array());
    }
    if( !is_admin() ){
        wp_enqueue_style('nutrition-table',  $url.'../css/nutrition.css',__FILE__,array());
    }
}
function add_admin_css($hook) {
    $url = plugin_dir_url( __FILE__ );
    wp_enqueue_style('admin-css',  $url.'../css/admin.css',__FILE__,array());
}

function add_content_filter( $content ){


    // and custom data to the content if post is a product type post
    // so we dont need to create/use a custom template in the theme to show metadata of the product
    global $post;

    $new_content = null;
    //error_log(get_post_type( $post->ID ));
    if ( get_post_type( $post->ID ) == 'products' ) { //if post is not a product just return the content
        $rating = 0;
        if (get_post_meta($post->ID, 'rating', true)) {
            $rating = get_post_meta($post->ID, 'rating', true);
        }
        $brand = 0;
        if (get_post_meta($post->ID, 'brand', true)) {
            $brand = get_post_meta($post->ID, 'brand', true);
        }
        $serving_size = 0;
        if (get_post_meta($post->ID, 'serving-size', true)) {
            $serving_size = get_post_meta($post->ID, 'serving-size', true);
        }
        $calories = 0;
        if (get_post_meta($post->ID, 'calories', true)) {
            $calories = get_post_meta($post->ID, 'calories', true);
        }
        $calories_fat = 0;
        if (get_post_meta($post->ID, 'calories-fat', true)) {
            $calories_fat = get_post_meta($post->ID, 'calories-fat', true);
        }
        $total_fat_gr = 0;
        if (get_post_meta($post->ID, 'total-fat-gr', true)) {
            $total_fat_gr = get_post_meta($post->ID, 'total-fat-gr', true);
        }
        $total_fat_pc = 0;
        if (get_post_meta($post->ID, 'total-fat-pc', true)) {
            $total_fat_pc = get_post_meta($post->ID, 'total-fat-pc', true);
        }
        $satured_fat_gr = 0;
        if (get_post_meta($post->ID, 'satured-fat-gr', true)) {
            $satured_fat_gr = get_post_meta($post->ID, 'satured-fat-gr', true);
        }
        $satured_fat_pc = 0;
        if (get_post_meta($post->ID, 'satured-fat-pc', true)) {
            $satured_fat_pc = get_post_meta($post->ID, 'satured-fat-pc', true);
        }
        $trans_fat = 0;
        if (get_post_meta($post->ID, 'trans-fat', true)) {
            $trans_fat = get_post_meta($post->ID, 'trans-fat', true);
        }
        $cholesterol_gr = 0;
        if (get_post_meta($post->ID, 'cholesterol-gr', true)) {
            $cholesterol_gr = get_post_meta($post->ID, 'cholesterol-gr', true);
        }
        $cholesterol_pc = null;
        if (get_post_meta($post->ID, 'cholesterol-pc', true)) {
            $cholesterol_pc = get_post_meta($post->ID, 'cholesterol-pc', true);
        }
        $sodium_gr = 0;
        if (get_post_meta($post->ID, 'sodium-gr', true)) {
            $sodium_gr = get_post_meta($post->ID, 'sodium-gr', true);
        }
        $sodium_pc = null;
        if (get_post_meta($post->ID, 'sodium-pc', true)) {
            $sodium_pc = get_post_meta($post->ID, 'sodium-pc', true);
        }
        $carbohydrate_gr = 0;
        if (get_post_meta($post->ID, 'carbohydrate-gr', true)) {
            $carbohydrate_gr = get_post_meta($post->ID, 'carbohydrate-gr', true);
        }
        $carbohydrate_pc = 0;
        if (get_post_meta($post->ID, 'carbohydrate-pc', true)) {
            $carbohydrate_pc = get_post_meta($post->ID, 'carbohydrate-pc', true);
        }
        $fiber_gr = 0;
        if (get_post_meta($post->ID, 'fiber-gr', true)) {
            $fiber_gr = get_post_meta($post->ID, 'fiber-gr', true);
        }
        $fiber_pc = 0;
        if (get_post_meta($post->ID, 'fiber-pc', true)) {
            $fiber_pc = get_post_meta($post->ID, 'fiber-pc', true);
        }
        $sugars = 0;
        if (get_post_meta($post->ID, 'sugars', true)) {
            $sugars = get_post_meta($post->ID, 'sugars', true);
        }
        $protein = null;
        if (get_post_meta($post->ID, 'protein', true)) {
            $protein = get_post_meta($post->ID, 'protein', true);
        }
        $vitamina = 0;
        if (get_post_meta($post->ID, 'vitamina', true)) {
            $vitamina = get_post_meta($post->ID, 'vitamina', true);
        }
        $vitaminc = 0;
        if (get_post_meta($post->ID, 'vitaminc', true)) {
            $vitaminc = get_post_meta($post->ID, 'vitaminc', true);
        }
        $calcium = 0;
        if (get_post_meta($post->ID, 'calcium', true)) {
            $calcium = get_post_meta($post->ID, 'calcium', true);
        }
        $iron = 0;
        if (get_post_meta($post->ID, 'iron', true)) {
            $iron = get_post_meta($post->ID, 'iron', true);
        }
        $new_content .= '<div class="product-boxed">';
        $new_content .= '<div class="product-left-box">';

        $new_content .= '<div class="col-md-12 shop-item product-detail-card">';
        $new_content .= '<div class="card card-product">';
        $new_content .= '<div class="card-image">';
        $new_content .= get_the_post_thumbnail( $post->ID, array( 150, 150) );
        $new_content .= '<div class="ripple-container"></div>';
        $new_content .= '</div>';

        $new_content .= '<div class="content">';
        $new_content .= '<h4 class="card-title">';
        $new_content .=  get_the_title();
        $new_content .= '</h4>';
        $new_content .= '<div class="card-description">';
        if ($rating) {
                $rating_array = product_ratings();
                $rating_output = $rating_array[$rating]['output'];
                $new_content .= '<div class="product-title-text"><b>Product Rating: </b>' . wp_kses_post($rating_output) . '</div>';
        }
         if ($brand) {
            $new_content .= '<div class="product-title-text"><b>Product Brand: </b>' . wp_kses_post(get_the_title($brand)) . '</div></br>';
        }
       

        $new_content .= '</div>';
        $new_content .= '</div>';
        $new_content .= '</div>';
        $new_content .= '</div>';

        // $new_content .= '<div class="product-title-text">Product Name: ' . get_the_title() . '</div></br>';

        // if ($rating) {
        //     $rating_array = product_ratings();
        //     $rating_output = $rating_array[$rating]['output'];
        //     $new_content .= '<div class="product-title-text">Product Rating: ' . wp_kses_post($rating_output) . '</div></br>';
        // }

        // if ($brand) {
        //     $new_content .= '<div class="product-title-text">Product Brand: ' . wp_kses_post(get_the_title($brand)) . '</div></br>';
        // }


        $new_content .= '</div>';
        $new_content .= '<div class="product-left-box product-nutrition-fact">';
        
       
        $new_content .= '<div class="nutrition-fact-box"><p>'. __("Nutrition Facts","NicaSource").'</p>';

        $new_content .= '<div class="serving-size">'. __("Serving Size ","NicaSource") .  $serving_size . 'g</div>';
        $new_content .= '<hr class="big-separator">';

        $new_content .= '<div class="amount small-text">'. __("Amount per serving ","NicaSource").'</div>';
        $new_content .= '<hr>';

        $new_content .= '<div><div class="row-left"><div class="calories box-title-black">'. __("Calories ","NicaSource") . $calories . '</div></div>';
        $new_content .= '<div class="row-right"><div class="calories box-title">'. __("Calories from Fat ","NicaSource") . $calories_fat . '</div></div><div class="clear"></div></div>';
        $new_content .= '<hr class="medium-separator">';

        $new_content .= '<div class="daily box-title">'. __("% Daily Value *","NicaSource") .'</div>';
        $new_content .= '<hr>';

        $new_content .= '<div><div class="row-left"><div class="total-fat box-title-black">'. __("Total Fat ","NicaSource") . $total_fat_gr . 'g</div></div>';
        $new_content .= '<div class="row-right"><div class="total-fat-pc box-title">' . $total_fat_pc . '%</div></div><div class="clear"></div></div>';
        $new_content .= '<hr>';

        $new_content .= '<div><div class="row-left"><div class="satured-fat box-title">'. __("Satured Fat ","NicaSource") . $satured_fat_gr . 'g</div></div>';
        $new_content .= '<div class="row-right"><div class="satured-fat-pc box-title">' . $satured_fat_pc . '%</div></div><div class="clear"></div></div>';
        $new_content .= '<hr>';

        $new_content .= '<div class="trans-fat box-title">'. __("Trans Fat ","NicaSource")  . $trans_fat . 'g</div>';
        $new_content .= '<hr>';

        $new_content .= '<div><div class="row-left"><div class="cholesterol-gr box-title-black">'. __("Cholesterol ","NicaSource") . $cholesterol_gr . 'g</div></div>';
        $new_content .= '<div class="row-right"><div class="cholesterol-pc box-title">' . $cholesterol_pc . '%</div></div><div class="clear"></div></div>';
        $new_content .= '<hr>';

        $new_content .= '<div><div class="row-left"><div class="sodium-gr box-title-black">'. __("Sodium ","NicaSource")  . $sodium_gr . 'g</div></div>';
        $new_content .= '<div class="row-right"><div class="sodium-pc box-title">' . $sodium_pc . '%</div></div><div class="clear"></div></div>';
        $new_content .= '<hr>';

        $new_content .= '<div><div class="row-left"><div class="carbohydrate-gr box-title-black">'. __("Total Carbohydrate ","NicaSource") . $carbohydrate_gr . 'g</div></div>';
        $new_content .= '<div class="row-right"><div class="carbohydrate-pc box-title">' . $carbohydrate_pc . '%</div></div><div class="clear"></div></div>';
        $new_content .= '<hr>';

        $new_content .= '<div><div class="row-left"><div class="dietary-fiber box-title">'. __("Dietary Fiber ","NicaSource") . $fiber_gr . 'g</div></div>';
        $new_content .= '<div class="row-right"><div class="dietary-fiber-pc box-title">' . $fiber_pc . '%</div></div><div class="clear"></div></div>';
        $new_content .= '<hr>';

        $new_content .= '<div class="sugars box-title">'. __("Sugars ","NicaSource") . $sugars . 'g</div>';
        $new_content .= '<hr>';

        $new_content .= '<div class="protein box-title-black">'. __("Protein ","NicaSource") . $protein . 'g</div>';
        $new_content .= '<hr class="big-separator">';

        $new_content .= '<div><div class="row-left"><div class="vitamina box-title">'. __("Vitamin A ","NicaSource") . $vitamina . '%</div></div>';
        $new_content .= '<div class="row-right"><div class="vitaminc box-title">'. __(" Vitamin C ","NicaSource") . $vitaminc . '%</div></div><div class="clear"></div></div>';
        $new_content .= '<hr>';

        $new_content .= '<div><div class="row-left"><div class="calcium box-title">'. __("Calcium ","NicaSource") . $calcium . '%</div></div>';
        $new_content .= '<div class="row-right"><div class="vitaminc box-title"> '. __("Iron ","NicaSource") . $iron . '%</div></div><div class="clear"></div></div>';
        $new_content .= '<hr>';

        $new_content .= '<p class="small">'. __("*Percent Daily Values are based on a 2,000 calories diet. Your daily values may be higher or lower depending on your calorie needs","NicaSource").'</p>';
        $new_content .= '<div class="nutri-box">'. __("NutritionData.com","NicaSource") .'</div>';

        $new_content .= '</div>';
        $new_content .= '</div>';
        $new_content .= '</div>';
        $new_content .= '<h2 class="product_title entry-title">Description:</h2>';

    }

    //return the content with custom content
    return  $new_content.$content;

}

function ns_nutrition_facts(){

    //* html of nutritions fact in the admin
    global $post;
    echo '<div class="sm-row-content"> <label for="serving-size">'. __("Serving Size ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("Value in grams", "NicaSource").'" id="serving-size" name="serving-size" value="'.htmlspecialchars( get_post_meta( $post->ID, 'serving-size', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="calories">'. __("Calories ","NicaSource").' </label></br>';
    echo '<input type="text" placeholder="'. __("Value of calories", "NicaSource").'" id="calories" name="calories" value="'.htmlspecialchars( get_post_meta( $post->ID, 'calories', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="calories-fat">'. __("Calories from Fat ","NicaSource").'</label></br>';
    echo '<input type="text" placeholder="'. __("Value of calories from Fat", "NicaSource").'" id="calories-fat" name="calories-fat" value="'.htmlspecialchars( get_post_meta( $post->ID, 'calories-fat', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="total-fat">'. __("Total Fat ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("Value in grams", "NicaSource").'" id="total-fat-gr" name="total-fat-gr" value="'.htmlspecialchars( get_post_meta( $post->ID, 'total-fat-gr', true) ).'"/>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'" id="total-fat-pc" name="total-fat-pc" value="'.htmlspecialchars( get_post_meta( $post->ID, 'total-fat-pc', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="satured-fat">'. __("Satured Fat ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("Value in grams", "NicaSource").'" id="satured-fat-gr" name="satured-fat-gr" value="'.htmlspecialchars( get_post_meta( $post->ID, 'satured-fat-gr', true) ).'"/>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'"  id="satured-fat-pc" name="satured-fat-pc" value="'.htmlspecialchars( get_post_meta( $post->ID, 'satured-fat-pc', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="trans-fat">'. __("Trans Fat","NicaSource")  .'</label></br>';
    echo '<input type="text" id="trans-fat" name="trans-fat" value="'.htmlspecialchars( get_post_meta( $post->ID, 'trans-fat', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="cholesterol">'. __("Cholesterol ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("Value in milligrams", "NicaSource").'" id="cholesterol-gr" name="cholesterol-gr" value="'.htmlspecialchars( get_post_meta( $post->ID, 'cholesterol-gr', true) ).'"/>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'" id="cholesterol-pc" name="cholesterol-pc" value="'.htmlspecialchars( get_post_meta( $post->ID, 'cholesterol-pc', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="sodium">'. __("Sodium ","NicaSource")  .'</label></br>';
    echo '<input type="text" placeholder="'. __("Value in milligrams", "NicaSource").'" id="sodium-gr" name="sodium-gr" value="'.htmlspecialchars( get_post_meta( $post->ID, 'sodium-gr', true) ).'"/>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'" id="sodium-pc" name="sodium-pc" value="'.htmlspecialchars( get_post_meta( $post->ID, 'sodium-pc', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="carbohydrate">'. __("Total Carbohydrate ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("Value in grams", "NicaSource").'" id="carbohydrate-gr" name="carbohydrate-gr" value="'.htmlspecialchars( get_post_meta( $post->ID, 'carbohydrate-gr', true) ).'"/>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'" id="carbohydrate-pc" name="carbohydrate-pc" value="'.htmlspecialchars( get_post_meta( $post->ID, 'carbohydrate-pc', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="Fiber">'. __("Dietary Fiber ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("Value in grams", "NicaSource").'" id="fiber-gr" name="fiber-gr" value="'.htmlspecialchars( get_post_meta( $post->ID, 'fiber-gr', true) ).'"/>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'" id="fiber-pc" name="fiber-pc" value="'.htmlspecialchars( get_post_meta( $post->ID, 'fiber-pc', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="sugars">'. __("Sugars ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("Value in grams", "NicaSource").'"  id="sugars" name="sugars" value="'.htmlspecialchars( get_post_meta( $post->ID, 'sugars', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="protein">'. __("Protein ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("Value in grams", "NicaSource").'" id="protein" name="protein" value="'.htmlspecialchars( get_post_meta( $post->ID, 'protein', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="vitamina">'. __("Vitamin A ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'" id="vitamina" name="vitamina" value="'.htmlspecialchars( get_post_meta( $post->ID, 'vitamina', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="vitaminc">'. __("Vitamin C ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'" id="vitaminc" name="vitaminc" value="'.htmlspecialchars( get_post_meta( $post->ID, 'vitaminc', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="calcium">'. __("Calcium ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'" id="calcium" name="calcium" value="'.htmlspecialchars( get_post_meta( $post->ID, 'calcium', true) ).'"/>';
    echo '</div>';

    echo '<div class="sm-row-content"> <label for="iron">'. __("Iron ","NicaSource") .'</label></br>';
    echo '<input type="text" placeholder="'. __("% value ", "NicaSource").'" id="iron" name="iron" value="'.htmlspecialchars( get_post_meta( $post->ID, 'iron', true) ).'"/>';
    echo '</div>';

}


function ns_product_meta()
{
    //metabox for brand, rating and featured options
    global $post;
    $postmeta = get_post_meta($post->ID);
    ?>
    <p>
    <div class="sm-row-content">
        <label for="featured">
            <input type="checkbox" name="featured" id="featured"
                   value="yes" <?php if (isset ($postmeta['featured'])) checked($postmeta['featured'][0], 'yes'); ?> />
            <?php _e('Featured this post', 'NicaSource') ?>
        </label>

    </div>
    <?php
    echo '<div class="sm-row-content"> <label for="rating">Rating</label></br>';
    echo '<select name="rating" id="rating">';
    $selected = get_post_meta($post->ID, 'rating', true);
    foreach (product_ratings() as $option) {
        $label = $option['label'];
        $value = $option['value'];
        echo '<option value="' . $value . '" ' . selected($selected, $value) . '>' . $label . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '<div class="sm-row-content"> <label for="rating">Brand</label></br>';
    echo '<select name="brand" id="brand">';
    $selectedBrand = get_post_meta($post->ID, 'brand', true);
    foreach (product_brand() as $option){
        $label = $option['label'];
        $value = $option['value'];
        echo '<option value="' . $value . '" ' . selected($selectedBrand, $value) . '>' . $label . '</option>';
    }
    echo '</select>';
    echo '</div>';


}

function product_ratings()
{
    //simple array to feed the select option of rating and return html to add in front end to show icons, font-awesome icons
    $ratings = array(
        1 => array(
            'label' => __('One Star'),
            'value' => 1,
            'output' => '<i class="fa fa-star" aria-hidden="true"></i>'
        ),
        2 => array(
            'label' => __('Two Stars'),
            'value' => 2,
            'output' => '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>'
        ),
        3 => array(
            'label' => __('Three Stars'),
            'value' => 3,
            'output' => '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>'
        ),
        4 => array(
            'label' => __('Four Starts'),
            'value' => 4,
            'output' => '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>'
        ),
        5 => array(
            'label' => __('Five Starts'),
            'value' => 5,
            'output' => '<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>'
        ),
    );
    return $ratings;
}
function product_brand(){

    // function to get the post brands by name and feed the brand select in admin page
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
        array_push($brand_list, array('label' => $brand_name, 'value' => $brand_id));
    }

    wp_reset_query();

    return $brand_list;
}

function ns_save_meta()
{
    //save custom meta data on post save
    global $post;
    if (!current_user_can('edit_post', $post->ID))
        return $post->ID;


    $meta_keys = array(
        'featured' => 'text',
        'rating' => 'numeric',
        'brand' => 'numeric',
        'serving-size' => 'numeric',
        'calories' => 'numeric',
        'calories-fat' => 'numeric',
        'total-fat-gr' => 'numeric',
        'total-fat-pc' => 'numeric',
        'satured-fat-gr' => 'numeric',
        'satured-fat-pc' => 'numeric',
        'trans-fat' => 'numeric',
        'cholesterol-gr' => 'numeric',
        'cholesterol-pc' => 'numeric',
        'sodium-gr' => 'numeric',
        'sodium-pc' => 'numeric',
        'carbohydrate-gr' => 'numeric',
        'carbohydrate-pc' => 'numeric',
        'fiber-gr' => 'numeric',
        'fiber-pc' => 'numeric',
        'sugars' => 'numeric',
        'protein' => 'numeric',
        'vitamina' => 'numeric',
        'vitaminc' => 'numeric',
        'calcium' => 'numeric',
        'iron' => 'numeric',
    );

    foreach($meta_keys as $meta_key => $meta_value) {
        if ($post->post_type == 'revision')
            return;

        if (isset($_POST[$meta_key])) {
            if ($meta_value == 'text') {
                $value = wp_kses_post($_POST[$meta_key]);
            }

            if ($meta_value == 'numeric' && is_numeric($_POST[$meta_key])) {
                $value = wp_kses_post($_POST[$meta_key]);
            }
            update_post_meta($post->ID, $meta_key, $value);
            $value = '';
        }else{
            delete_post_meta($post->ID, $meta_key);
        }
    }
}
