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
add_action( 'admin_menu', 'praywithus_menu' );

function praywithus_menu() {
	add_options_page( 'Prayer Options', 'Pray With Us', 'manage_options', 'praywithus', 'praywithus_options' );
}

function praywithus_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }

  // insert new post
  if ( isset($_POST['title']) && isset($_POST['description']) ) {
    praywithus_create_request($_POST['title'], $_POST['description']);
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
   foreach ( $requests as $r ) {
    echo "<dt>" . $r->title. "</dt>";
    echo '<dd>';
    echo '  <div><em>' . $r->description . '</em></div>';
    echo '  <div><b>N praying</b> &ndash; Activate | Deactivate | Delete</div>';
    echo '</dd>';
  }
?>
  </dl>
</div>
<?php
}
?>