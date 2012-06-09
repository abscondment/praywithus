<?php
/*  Copyright 2012 Brendan Ribera (email : brendan.ribera@gmail.com)

    Permission is hereby granted, free of charge, to any person obtaining
    a copy of this software and associated documentation files (the
    "Software"), to deal in the Software without restriction, including
    without limitation the rights to use, copy, modify, merge, publish,
    distribute, sublicense, and/or sell copies of the Software, and to
    permit persons to whom the Software is furnished to do so, subject to
    the following conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
    LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
    OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
    WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/**
 * @package PrayWithUs
 */

require_once dirname( __FILE__ ) . '/db.php';

$praywithus_session = NULL;

class PrayWithUs_Widget extends WP_Widget {
  function __construct() {
    parent::__construct(
                        'praywithus_widget',
                        __( 'Pray With Us' ),
                        array( 'description' => __( 'Display Prayer Requests and counts' ) )
                        );

    if ( is_active_widget( false, false, $this->id_base ) ) {
      add_action( 'wp', array( $this, 'cookies' ) );
      add_action( 'wp_head', array( $this, 'cssjs' ) );
      add_action( 'wp_ajax_nopriv_pray', array ( $this, 'pray_submit' ) );
    }
  }
  function cssjs() {
    wp_register_script( 'praywithus.js', PRAYWITHUS_PLUGIN_URL . 'praywithus.js', array('jquery'), PRAYWITHUS_VERSION );
    wp_enqueue_script( 'praywithus.js' );
    wp_localize_script( 'praywithus.js', 'PrayWithUs', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
?>
<style type="text/css">
.ul-addw2p {margin:0; padding:0;}
.praywithus dl, .praywithus dl dt,  .praywithus dl dd {
    margin:0;
    padding:0;
}
    .praywithus dl dd { margin-bottom:1em; }
</style>
<?php

  }

  function cookies() {
    global $praywithus_session;
    if ( isset($_COOKIE["wp_praywithus_session"]) ) {
      $praywithus_session = $_COOKIE["wp_praywithus_session"];
    }
    if ( empty($praywithus_session) ) {
      $praywithus_session = uniqid($_SERVER['REMOTE_ADDR'] . '_', true);
    }
    setcookie("wp_praywithus_session", $praywithus_session, time() + 631138519);  /* expire in 20 years */
  }
  
  function widget( $args, $instance ) {
    global $praywithus_session;
    
    // load active requests
    $requests = praywithus_get_active_requests();
?>
<div class="wrap praywithus">
  <dl>
<?php
   $i = 0;
   foreach ( $requests as $r ) {
     $alt = $i % 2 == 1;

     $cssClasses = 'request';

     if ( $alt ) {
       $cssClasses .= ' alt';
     }
     echo "<dt class='$cssClasses'>";
     echo "<b>" . $r->title. "</b>";
     echo "</dt>";
     echo "<dd class='$cssClasses'>";
     echo $r->description;
     echo '<br/>';
     echo " <b><span class='count'>$r->count</span> praying</b> <span class='hidePost'>&ndash;</span> ";
     echo "<a href='#' class='hidePost praywithusButton' onclick='return false;' id='$r->id'>Pray</a>";
     echo '</dd>';
     $i++;
  }
?>
  </dl>
</div>
<?php
  }

  function pray_submit() {
    global $praywithus_session;
    if ( isset($_COOKIE["wp_praywithus_session"]) ) {
      $praywithus_session = $_COOKIE["wp_praywithus_session"];
    }    

    // get the submitted parameters
    $requestID = $_POST['requestID'];

    // insert it
    praywithus_add_prayer($requestID, $praywithus_session);

    // generate the response
    $response = json_encode( array( 'success' => true, 'count' => 999 ) );

    // response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
    exit;
  }
}

function praywithus_register_widgets() {
  register_widget( 'PrayWithUs_Widget' );
}

add_action( 'widgets_init', 'praywithus_register_widgets' );

?>