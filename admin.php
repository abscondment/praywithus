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

require_once dirname( __FILE__ ) . '/db.php';
add_action( 'admin_menu', 'load_praywithus_menu' );
add_action( 'admin_enqueue_scripts', 'praywithus_load_js_and_css' );

function load_praywithus_menu() {
  add_menu_page( 'Manage Prayer Requests', 'Pray With Us', 'edit_posts', 'praywithus_menu', 'praywithus_menu', plugin_dir_url( __FILE__ ) . 'praywithus.png', 37 );
}

function praywithus_load_js_and_css() {
  wp_register_style( 'praywithus.css', PRAYWITHUS_PLUGIN_URL . 'praywithus.css', array(), PRAYWITHUS_VERSION );
  wp_enqueue_style( 'praywithus.css');
}

function praywithus_menu() {
  // insert new post
  if ( isset($_POST['title']) && !empty($_POST['title']) && isset($_POST['description']) && !empty($_POST['description']) ) {
    praywithus_create_request($_POST['title'], $_POST['description']);
  } else if ( isset($_POST['toggleRequest']) && !empty($_POST['toggleRequest']) && isset($_POST['toggleTo']) && !empty($_POST['toggleTo']) ) {
    if ( $_POST['toggleTo'] == 'activate' ) {
      praywithus_actvitate_request(intval($_POST['toggleRequest']));
    } else {
      praywithus_deactvitate_request(intval($_POST['toggleRequest']));
    }
  } else if ( isset($_POST['deleteRequest']) && !empty($_POST['deleteRequest']) ) {
    praywithus_delete_request(intval($_POST['deleteRequest']));
  }
  
  // load current posts
  $requests = praywithus_get_requests();
?>    
<div class="wrap">
  <h2>Manage Prayer Requests</h2>
   <script type="text/javascript">
     var PWUactuallyDelete = function(el) {
       return confirm("Do you really want to delete this request?")
    }
  </script>
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
     echo '</form>';
     echo ' | ';
     echo "<form action='' method='POST' onsubmit='return PWUactuallyDelete(this);'><input type='hidden' name='deleteRequest' value='$r->id'  />";
     echo "<input type='submit' value='Delete' />";
     echo '</form>';
     echo'</div>';
     echo '</dd>';
     $i++;
  }
?>
  </dl>

  <hr />
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
</div>
<?php
}
?>