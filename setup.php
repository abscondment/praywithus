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

global $praywithus_db_version;
$praywithus_db_version = "0.0.2";

require_once dirname( __FILE__ ) . '/db.php';
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

function praywithus_install () {
   global $wpdb;
   global $praywithus_db_version;

  $installed_ver = get_option( "praywithus_db_version" );
  
  if( $installed_ver != $praywithus_db_version ) {
     $request_table = $wpdb->prefix . "praywithus_requests";
     $prayers_table = $wpdb->prefix . "praywithus_prayers";
     $sql = "
  CREATE TABLE $request_table (
    id mediumint(11) NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    description TEXT,
    active TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL,
    created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY id (id)
  );
  CREATE TABLE $prayers_table (
    id mediumint(11) NOT NULL AUTO_INCREMENT,  
    session_id varchar(255) NOT NULL,
    request_id mediumint(11) NOT NULL,
    PRIMARY KEY id (id),
    UNIQUE KEY prayer_id (request_id, session_id)
  );";
     
     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
     dbDelta($sql);

     praywithus_install_data();
  }

  if ( empty($installed_ver) ) {
    add_option("praywithus_db_version", $praywithus_db_version);
  } else {
    update_option( "praywithus_db_version", $praywithus_db_version );
  }
}

function praywithus_install_data() {
  $welcome_title = "Example Prayer";
  $welcome_text = "This is an example prayer request. It shows that you have installed the Pray With Us plugin successfully!";
  praywithus_create_request($welcome_title, $welcome_text);
}
?>