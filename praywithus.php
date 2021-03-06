<?php
/*
Plugin Name: Pray With Us
Plugin URI: https://github.com/abscondment/praywithus
Description: List prayer requests with counts of people praying.
Version: 0.0.2
Author: Brendan Ribera
Author URI: http://threebrothers.org
License: MIT
*/
?>
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

include_once dirname( __FILE__ ) . '/widget.php';

if ( is_admin() ) {
  require_once dirname( __FILE__ ) . '/setup.php';
  register_activation_hook(__FILE__,'praywithus_install');

  require_once dirname( __FILE__ ) . '/admin.php';
}

?>
