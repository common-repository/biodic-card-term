<?php
/**
 * Plugin Name: BioDic Card Term
 * Plugin URI: https://www.biodic.net
 * Description: Este plugin sirve para mostrar una definición del proyecto Biodic. Para ello la palabra debe estar definida en nuestro proyecto para que pueda ser mostrada 
 * Version: 1.0.0
 * Author: BioScripts - Centro de Investigación y Desarrollo de Recursos Científicos
 * Author URI: https://www.bioscripts.net
 * License: GPL2
 * Text Domain: biodic-card-term
 */

 // Creamos el shortcode
function biodic_card_term( $atts) {

	// Atributos
	$atts = shortcode_atts(
		array(
			'name' => '',
			'show_origin' => '',
			'show_img' => '',
		),
		$atts,
		'biodic'
	);
	
	$name=sanitize_title($atts["name"]);
	$response = wp_remote_get( 'https://www.biodic.net/wp-json/wp/v2/palabra/?slug='.$name.'', array( 'timeout' => 120, 'httpversion' => '1.1' ) );
	if ( is_array( $response ) ) {
		$body = wp_remote_retrieve_body( $response );
		$obj = json_decode($body);
	}

	$title=mb_ucfirst($obj[0]->title->rendered);
	//Custom field for the future
	//$origin=$obj[0]->custom-field->origen;
	$definition=$obj[0]->content->rendered; 
	
	$return = '<div class="containerbiodic">
				<input type="radio" checked="checked" name="nav" id="one" />
				<label for=""></label>
				<div class="first">
				    <blockquote>
				      <span class="quotes leftq"> &ldquo;</span> '.$definition.' <span class="rightq quotes">&bdquo; </span>
				    </blockquote>
				    <h2>'.$title.'</h2>
				    <!-- h6><em>'.$origin.'</em></h6 -->
				  </div>
				</div>';

	return $return;

}
add_shortcode( 'biodic', 'biodic_card_term' );

/**
 * Incluimos la hoja de estilo.
 */
function biodic_style_css() {
    wp_register_style( 'biodic_style',  plugin_dir_url( __FILE__ ) . 'assets/biodic_style.css' );
    wp_enqueue_style( 'biodic_style' );
}
add_action( 'wp_enqueue_scripts', 'biodic_style_css' );

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false) {
      $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
      $str_end = "";
      if ($lower_str_end) {
        $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
      }
      else {
        $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
      }
      $str = $first_letter . $str_end;
      return $str;
    }
  }
?>