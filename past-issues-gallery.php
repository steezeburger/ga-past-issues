<?php
    /*
    Plugin Name: Past Issues Gallery
    Plugin URI: http://jessesnyder.me/wordpress
    Description: Plugin for displaying a grid of past issues. When an issue is clicked, it displays in a lightbox. Dependent on WonderPlugin Lightbox
    Author: Jesse Snyder
    Version: 0.1
    Author URI: http://www.jessesnyder.me
    */


function past_issues_admin() {
  // Generates html for admin page view
  include('admin/past_issues_import_admin.php');
}

function past_issues_admin_enqueue() {
  // If not on admin page view for the plugin, don't load styles/scripts
  $screen = get_current_screen();
  if( $screen->id != 'settings_page_past-issues-gallery') {
    return;
  }
  // Styles
  wp_register_style('past_issues_admin_styles', plugin_dir_url(__FILE__) . 'admin/css/past_issues_styles.css');
  wp_enqueue_style('past_issues_admin_styles');
  // WP doesn't include the necessary jquery-ui stylings. Google CDN ftw
  wp_enqueue_style('jquery_ui_styles', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css');
  // Scripts
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-widget');
  wp_enqueue_script('jquery-ui-mouse');
  wp_register_script('past_issues_admin_js',  plugin_dir_url(__FILE__) . 'admin/js/past_issues_admin.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse'));
  wp_enqueue_script('past_issues_admin_js');
}

// Add to admin scripts
add_action('admin_enqueue_scripts', 'past_issues_admin_enqueue');

// Add menu page
function past_issues_admin_functions() {
  add_options_page("Past Issues Gallery", "Past Issues Gallery", 1, "past-issues-gallery", "past_issues_admin");
}

add_action('admin_menu', 'past_issues_admin_functions');

// Shortcode generation
function past_issues_shortcode_handler($atts) {
  // Get current year
  $current_year = date("Y");
  // Defaults to current year if no year specified when shortcode is called
  $atts = shortcode_atts(array(
          'year' => $current_year)
          , $atts);
          
  // Config file location
  $config_file_loc = plugin_dir_path(__FILE__) . 'admin/configuration.json';

  // Get file contents as string and decode json into array of objects
  $videos = json_decode(file_get_contents($config_file_loc));

  // Create array and populate with all years from json config
  $year_array = [];
  foreach( $videos as $video ) {
    // Parse to get year
    $year = explode("/", $video->title)[2];
    // Push to array if not in array
    if ( !in_array( $year, $year_array ) ) {
      array_push( $year_array, $year );
    }
  }
    
  // Start output buffer
  ob_start();
  
  // If year given is current year, generate HTML that has most recent issue's html5 viewer up top with previous issue icons below
  if ($atts['year'] == $current_year) { ?>
      <h4>Current Issue</h4>
        <center>
          <iframe src="<?php echo $videos->vid0->youTubeURL; ?>" width="670" height="420" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
        </center>
      <h4>Previous Issues</h4>
      <ul class="rig columns-3">

      <?php 
      // Create icon entry for each video
      $counter = 0;
      foreach($videos as $video) :
        // Skip first issue because it is featured
        if ($counter++ == 0) continue;
        // Skip all issues not in specified year
        $issue_year = explode("/", $video->title)[2];
        if ($issue_year != $atts['year']) continue;
      ?>
        <li>
          <a class="wplightbox" title="<?php echo $video->title; ?>" href="<?php echo $video->youTubeURL; ?>" >
            <img src="<?php echo $video->iconURL; ?>" alt="<?php echo $video->title; ?>" height="151" />
          </a>
          <p><?php echo $video->title; ?></p>
        </li>
      <?php endforeach; ?>
      </ul>

    <?php                                       
  } else { ?>
      <ul class="rig columns-3">
      <?php 
      // Create icon entry for each video
      foreach($videos as $video) :
        // Skip all issues not in specified year
        $issue_year = explode("/", $video->title)[2];
        if ($issue_year != $atts['year']) continue;
      ?>
        <li>
          <a class="wplightbox" title="<?php echo $video->title; ?>" href="<?php echo $video->youTubeURL; ?>" >
            <img src="<?php echo $video->iconURL; ?>" alt="<?php echo $video->title; ?>" height="151" />
          </a>
          <p><?php echo $video->title; ?></p>
        </li>
      <?php endforeach; ?>
      </ul> 
    <?php
  } // end if else 
    // Generate links for all past years
    ?>
    
    <h4>Other Years:
    <?php foreach( $year_array as $year ) : ?>
     <?php if ( $year == $atts['year'] ) continue ; ?>
      <a href="<?php echo home_url('/digital-flipbook/' . $year); ?>">
        <?php echo $year ; ?>
      </a>
    <?php endforeach; ?>
    </h4>
  
  <?php  
  // End buffer and return contents
  $buffer_contents = ob_get_contents();
  ob_end_clean();
  return $buffer_contents;
} // past_issues_shortcode_handler()

add_shortcode( 'past-issues', 'past_issues_shortcode_handler' );

function past_issues_widget_shortcode () {
  // Config file location
  $config_file_loc = plugin_dir_path(__FILE__) . 'admin/configuration.json';

  // Get file contents as string and decode json into array of objects
  $issues = json_decode(file_get_contents($config_file_loc), true);
  $most_recent_iconURL = $issues["vid0"]["iconURL"];

  ob_start(); ?>

  <!--  Inline CSS, sorry -->
  <style>
    .digital-flipbook-widget {
      width: 300px;
      height: 275px;
      background-color: #eee;
      text-align: center;
      font-size: 12px;
    }
    .digital-flipbook-widget a {
      text-decoration: none;
      color: #ce171f;
    }
    .digital-flipbook-widget p {
      padding: 10px;
      width: 65%;
      margin: 0 auto;
    }
    .digital-flipbook-widget img {
      height: 70%;
      margin: 0 auto;
      box-shadow: 10px 10px 5px #888888;
    }
  </style>

  <div class="digital-flipbook-widget">
    <a href="http://okgazette.com/digital-flipbook">
      <p>Click here to view a digital replica of this week's issue.</p>
      <img src="<?php echo $most_recent_iconURL ?>" alt="Digital Flipbook">
    </a>
  </div>

  <?php
  $buffer_contents = ob_get_contents();
  ob_end_clean();
  return $buffer_contents;
}

add_shortcode( 'past-issues-widget', 'past_issues_widget_shortcode' );
?>