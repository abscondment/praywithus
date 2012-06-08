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
add_action( 'admin_menu', 'praywithus_menu' );

function praywithus_menu() {
	add_options_page( 'Prayer Options', 'Pray With Us', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
}

function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
?>    
<div class="wrap">
  <h1>Configure Your Prayer Requests</h1>
  <dl>
    <dt>The Gift of Prayer</dt>
    <dd>
      <div><em>Donate 3 minutes or more of prayer one time.</em></div>
      <div><b>5 praying</b> &ndash; Activate | Deactivate | Delete</div>
    </dd>

    <dt>The Gift of Prayer</dt>
    <dd>
      <div><em>Donate 3 minutes or more of prayer one time.</em></div>
      <div><b>5 praying</b> &ndash; Activate | Deactivate | Delete</div>
    </dd>
  </dl>
</div>
<?php
}
?>