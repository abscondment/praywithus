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


define('PRAYWITHUS_VERSION', '0.0.2');
define('PRAYWITHUS_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once dirname( __FILE__ ) . '/db.php';
add_action( 'admin_menu', 'praywithus_menu' );
add_action( 'admin_enqueue_scripts', 'praywithus_load_js_and_css' );

function praywithus_menu() {
	add_options_page( 'Prayer Options', 'Pray With Us', 'manage_options', 'praywithus', 'praywithus_options' );
}

function praywithus_load_js_and_css() {
	// global $hook_suffix;

	// if (
	// 	$hook_suffix == 'index.php'	# dashboard
	// ) {
		wp_register_style( 'praywithus.css', PRAYWITHUS_PLUGIN_URL . 'praywithus.css', array(), PRAYWITHUS_PLUGIN_URL );
		wp_enqueue_style( 'praywithus.css');

        // TODO
		// wp_register_script( 'praywithus.js', PRAYWITHUS_PLUGIN_URL . 'praywithus.js', array('jquery'), '2.5.4.6' );
		// wp_enqueue_script( 'praywithus.js' );
		// wp_localize_script( 'praywithus.js', 'WPPraywithus', array(
		// 	'comment_author_url_nonce' => wp_create_nonce( 'comment_author_url_nonce' )
		// ) );
	// }
}

function praywithus_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }

  // insert new post
  if ( isset($_POST['title']) && isset($_POST['description']) ) {
    praywithus_create_request($_POST['title'], $_POST['description']);
  }

  if ( isset($_POST['toggleRequest']) && isset($_POST['toggleTo']) ) {
    if ( $_POST['toggleTo'] == 'activate' ) {
      praywithus_actvitate_request(intval($_POST['toggleRequest']));
    } else {
      praywithus_deactvitate_request(intval($_POST['toggleRequest']));
    }
  }

  // load current posts
  $requests = praywithus_get_requests();
?>    
<div class="wrap">
  <h2>Manage Prayer Requests</h2>

  <div id="addForm" style="display:none;">>
  <a href="#" onclick="return false;"><h3>Add a request</h3></a>
  </div>
    <h3>Add a request</h3>
    <form action="" method="POST">
      <dl>
        <dt><label for="request_title">Title: </label></dt>
        <dd><input id="request_title" name="title" style="min-width:150px;" /></dd>
        <dt><label for="request_description">Description: </label></dt>
        <dd><textarea id="request_description" name="description" style="min-height:100px;min-width:300px;"></textarea></dd>
      <dt></dt>
      <dd><input type="submit" value="Add Request" /></dd>
      </dl>
    </form>
    <hr />

  <h3>Current requests</h3>
  <dl>
<?php
   $i = 0;
   foreach ( $requests as $r ) {
     $active = intval($r->active) == 1;
     $alt = $i % 2 == 1;

     $cssClasses = 'request';

     if ( $alt ) {
       $cssClasses .= ' alt';
     }
     if ( !$active ) {
       if ( $alt ) {
         $cssClasses .= ' inactivealt';
       } else {
         $cssClasses .= ' inactive';
       }
     }


     echo "<dt class='$cssClasses'><b>" . $r->title. "</b> (" . ($active ? 'Active' : 'Inactive') . ")</dt>";
     echo "<dd class='$cssClasses'>";
     echo '  <div><em>' . $r->description . '</em></div>';

     echo '  <div>';
     echo "<form action='' method='POST'><input type='hidden' name='toggleRequest' value='$r->id' />";
     echo "<b>$r->count praying</b> &ndash; ";
     // toggle activation status

     if ( $active ) {
       // active
       echo "<input type='hidden' name='toggleTo' value='deactivate' />";
       echo "<input type='submit' value='Dectivate' />";

     } else {
       // inactive
       echo "<input type='hidden' name='toggleTo' value='activate' />";
       echo "<input type='submit' value='Activate' />";
      
     }
     echo ' | Delete</div>';
     echo '</form>';
     echo '</dd>';
     $i++;
  }
?>
  </dl>
</div>
<?php
}
?>