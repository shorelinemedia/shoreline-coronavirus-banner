<?php
/**
* Plugin Name:          Shoreline Coronavirus Banner
* Plugin URI:           https://github.com/shorelinemedia/shoreline-coronavirus-banner
* Description:          Add a banner to a WP site that lets you customize the banner text and add a link/button
* Version:              1.0.1
* Author:               Shoreline Media
* Author URI:           https://shoreline.media
* License:              GNU General Public License v2
* License URI:          http://www.gnu.org/licenses/gpl-2.0.html
* Text Domain:          shoreline-coronavirus-banner
* GitHub Plugin URI:    https://github.com/shorelinemedia/shoreline-coronavirus-banner
*/

// Customizer scoped to 'editor' user role to set true/false about test kit availability
if (!function_exists( 'shoreline_coronavirus_banner' ) ) {
  function shoreline_coronavirus_banner( $wp_customize ) {

    $wp_customize->add_section( 'shoreline_coronavirus_banner_section', array(
      'title' => __( 'Coronavirus Banner' ),
      'description' => __( 'Update the Coronavirus sitewide banner settings' ),
      'panel' => '', // Not typically needed.
      'priority' => 160,
      // Authors can access this section
      'capability' => 'edit_published_posts',
      'theme_supports' => '', // Rarely needed.
    ) );


    // Enable banner
    $wp_customize->add_setting( 'shoreline_coronavirus_enable_banner', array(
      'capability' => 'edit_published_posts',
      'sanitize_callback' => 'shoreline_coronavirus_banner_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'shoreline_coronavirus_enable_banner', array(
      'type' => 'checkbox',
      'section' => 'shoreline_coronavirus_banner_section', // Add a default or your own section
      'label' => __( 'Enable sitewide Coronavirus banner?' ),
      'description' => __( 'Once enabled, your site will have a banner on the top of the site with the information used below. Do not enable until link and text are accurate.' ),
    ) );

    // Enable sticky banner
    $wp_customize->add_setting( 'shoreline_coronavirus_enable_sticky', array(
      'capability' => 'edit_published_posts',
      'sanitize_callback' => 'shoreline_coronavirus_banner_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'shoreline_coronavirus_enable_sticky', array(
      'type' => 'checkbox',
      'section' => 'shoreline_coronavirus_banner_section', // Add a default or your own section
      'label' => __( 'Make the banner sticky?' ),
      'description' => __( 'Once enabled, your banner will stick to the top of the screen when the user scrolls. Make sure it doesn\'t compete with a stick main menu/navigation before activating.' ),
    ) );

    // // Sticky position
    // $wp_customize->add_setting( 'shoreline_coronavirus_sticky_position', array(
    //   'capability' => 'edit_published_posts',
    //   'default' => 'top'
    //
    // ) );
    //
    // $wp_customize->add_control( 'shoreline_coronavirus_sticky_position', array(
    //   'type' => 'select',
    //   'section' => 'shoreline_coronavirus_banner_section', // Add a default or your own section
    //   'label' => __( 'Sticky position' ),
    //   'description' => __( 'Stick the banner to the bottom or top of the website' ),
    //   'choices' => array(
    //     'top' => __( 'Top' ),
    //     'bottom' => __( 'Bottom' )
    //   )
    // ) );

    // Banner Text
    $wp_customize->add_setting( 'shoreline_coronavirus_banner_text', array(
      'capability' => 'edit_published_posts'
    ) );

    $wp_customize->add_control( 'shoreline_coronavirus_banner_text', array(
      'type' => 'text',
      'section' => 'shoreline_coronavirus_banner_section', // Add a default or your own section
      'label' => __( 'Banner Text' )
    ) );

    // Banner Link
    $wp_customize->add_setting( 'shoreline_coronavirus_banner_link', array(
      'capability' => 'edit_published_posts'
    ) );

    $wp_customize->add_control( 'shoreline_coronavirus_banner_link', array(
      'type' => 'url',
      'section' => 'shoreline_coronavirus_banner_section', // Add a default or your own section
      'label' => __( 'Button Link' ),
      'description' => __( 'If you\'d like to link to a page or article, include the URL here and a button will appear in the banner.' )
    ) );

  }
  add_action( 'customize_register', 'shoreline_coronavirus_banner' );
}

// Sanitize our checkbox in customizer
if (!function_exists( 'shoreline_coronavirus_banner_sanitize_checkbox' ) ) {
  function shoreline_coronavirus_banner_sanitize_checkbox( $checked ) {
    // Boolean check.
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
  }

}


// Register shortcode assets
if ( !function_exists( 'shoreline_coronavirus_banner_assets' ) ) {
  function shoreline_coronavirus_banner_assets() {
    wp_register_style( 'shoreline_coronavirus_banner', plugins_url( 'assets/css/covid-banner.css', __FILE__ ) );
  }
  add_action( 'wp_enqueue_scripts', 'shoreline_coronavirus_banner_assets' );
}

// Shortcode to output the banner
if ( !function_exists( 'sl9_covid_19_test_kits_banner_shortcode' ) ) {
  function sl9_covid_19_test_kits_banner_shortcode( $atts = array(), $content = null ) {
       extract(shortcode_atts(array(
          'text' => '',
       ), $atts));

       $html = $html_class = '';

       // Get Customizer/theme mod setting for kit status
       $enabled = get_theme_mod( 'shoreline_coronavirus_enable_banner' );

       // Don't output banner if it is not enabled
       if ( !$enabled ) return;

       // Enqueue styles
       wp_enqueue_style( 'shoreline_coronavirus_banner' );

       // Get the site title by default to use in banner text
       $site_title = get_bloginfo('name');
       // Set default text based on customizer checkbox
       $default_text = $site_title . ' is making efforts to contain the Coronavirus';
       // Use custom text if supplied, or else use default true/false text
       $customizer_text = get_theme_mod( 'shoreline_coronavirus_banner_text', '' );
       $text = !empty( $customizer_text ) ? $customizer_text : $default_text;

       // Get the optional link
       $link = get_theme_mod( 'shoreline_coronavirus_banner_link' );

       $link = !empty( $link ) ? $link : '';

       // SVG Icon
       $icon = file_get_contents( plugin_dir_path( __FILE__ ) . 'assets/images/icon-medical-test.svg' );

       // Is banner sticky?
       $is_sticky = get_theme_mod( 'shoreline_coronavirus_enable_sticky', false );
       if ( $is_sticky ) $html_class .= ' is-sticky';

       // // Sticky position (top by default)
       // $sticky_position = get_theme_mod( 'shoreline_coronavirus_sticky_position' , 'top' );
       // $html_class .=  $is_sticky ? ' is-sticky--' . $sticky_position : '';

       ob_start();
       // Build the HTML markup below and use the $text variable
       ?>

       <aside role="banner" class="coronavirus-banner <?php echo $html_class; ?>">
         <div class="coronavirus-banner__icon"><?php echo $icon; ?></div>
         <h2 class="coronavirus-banner__title"><strong>Coronavirus Alert:</strong> <?php echo $text; ?></h2>
         <?php if ( !empty( $link ) ) { ?>
           <a class="coronavirus-banner__button" href="<?php echo $link; ?>">Learn More</a>
         <?php } // endif is main site ?>
       </aside>

       <?php
       $html .= ob_get_clean();
       return do_shortcode( $html );
  }
  add_shortcode( 'sl9_coronavirus_banner', 'sl9_covid_19_test_kits_banner_shortcode', 10, 2 );
}

// Init actions
if ( !function_exists( 'shoreline_coronavirus_banner_init' ) ) {
  function shoreline_coronavirus_banner_init() {
    // Hook the shortcode output directly into the template using wp_body_open
    if ( function_exists( 'kleo_after_body' ) ) {
      add_action( 'kleo_after_body', 'shoreline_coronavirus_banner_add_to_body', 100 );
    } elseif ( function_exists( 'wp_body_open' ) ) {
      add_action( 'wp_body_open', 'shoreline_coronavirus_banner_add_to_body', 100 );
    }
  }
  add_action( 'init', 'shoreline_coronavirus_banner_init', 20 );
}

// Hook the banner into the body of the site
if ( !function_exists( 'shoreline_coronavirus_banner_add_to_body' ) ) {
  function shoreline_coronavirus_banner_add_to_body() {
    echo do_shortcode( '[sl9_coronavirus_banner]' );
  }
}
