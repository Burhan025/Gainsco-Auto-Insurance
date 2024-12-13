<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
} 
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'font-awesome' ); // FontAwesome 4
    wp_enqueue_style( 'font-awesome-5' ); // FontAwesome 5

    wp_dequeue_style( 'jquery-magnificpopup' );
    wp_dequeue_script( 'jquery-magnificpopup' );

    wp_dequeue_script( 'bootstrap' );
    //wp_dequeue_script( 'imagesloaded' );
    wp_dequeue_script( 'jquery-fitvids' );
    //wp_dequeue_script( 'jquery-throttle' );
    wp_dequeue_script( 'jquery-waypoints' );

    wp_enqueue_style( 'Barlow', '//fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600;700&display=swap', array() );

    wp_enqueue_style( 'Barlow-Condensed', '//fonts.googleapis.com/css2?family=Barlow+Condensed:wght@300;400;500;600;700&display=swap', array() ); 

    wp_enqueue_style( 'main', get_stylesheet_directory_uri() . '/css/main.css', array() );

    wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/js/main.js', array() ); 
    wp_localize_script( 'main', 'get_zipcode_url', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

}, 9999 );

function zipcode_shortcode() { 
$string = '';
$string .= '<div class="slider-loginbox">
<label>Get a Quick Quote</label>
<div class="banner-btn-wrapper">
<input id="zipcode" type="text" placeholder="Enter Zip Code" />
<span style="cursor:pointer;" id="yourlink" class="banner-button" href="#">Get Started Now</span>
</div>
</div>';

return $string;

} 
add_shortcode('zipcode', 'zipcode_shortcode'); 

add_action( 'wp_ajax_nopriv_get_zipcode_url', 'get_zipcode_url' );
add_action( 'wp_ajax_get_zipcode_url', 'get_zipcode_url' );
function get_zipcode_url(){
    $zipCode = (isset($_POST['zipCode']) ? $_POST['zipCode'] : '');
    $msg = array();
    if(!empty($zipCode)){
        $Url = "https://cpservice.gainsco.com/RedirectUrl/".$zipCode;
        $response = wp_remote_get($Url);
        if ( is_wp_error( $response ) ) {
            $msg['error'] = 'Data not available.';
        } else {
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body );
            $msg['result'] = $data;
            $msg['success'] = true;
        }
    }else{
        $msg['error'] = 'Enter valid zipcode';
    }
    echo json_encode($msg);
    wp_die();
}

/* AMP */

add_action('amp_init','amp_css', 11);
function amp_css() { 
    require_once('css/amp.php');
}

//* Add Featured Images to AMP content
add_action( 'pre_amp_render_post', 'amp_add_custom_actions' );
function amp_add_custom_actions() {
    add_filter( 'the_content', 'amp_add_featured_image' );
}

function amp_add_featured_image( $content ) {
    if ( has_post_thumbnail() ) {
        // Just add the raw <img /> tag; our sanitizer will take care of it later.
        $image = sprintf( '<p class="featured-image">%s</p>', get_the_post_thumbnail(get_the_ID(), 'amp') );
        $content = $image . $content;
    }
    return $content;
}