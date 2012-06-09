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


function praywithus_create_request($title, $description) {
  global $wpdb;
  $request_table = $wpdb->prefix . "praywithus_requests";
  $rows_affected = $wpdb->insert($request_table,
                                 array('created_at' => current_time('mysql'),
                                       'title' => $title,
                                       'description' => $description));
  return $rows_affected;
}


function praywithus_add_prayer($requestID, $sessionID) {
  global $wpdb;
  $prayers_table = $wpdb->prefix . "praywithus_prayers";
  $rows_affected = $wpdb->insert($prayers_table,
                                 array( 'request_id' => $requestID, 'session_id' => $sessionID ),
                                 array( '%d', '%s' ));
  return $rows_affected;
}


function praywithus_get_request($requestID) {
  global $wpdb;
  $request_table = $wpdb->prefix . "praywithus_requests";
  $prayers_table = $wpdb->prefix . "praywithus_prayers";
  $request = $wpdb->get_row( "SELECT r.*, ifnull(p.count, 0) as count
                              FROM $request_table r
                                LEFT OUTER JOIN
                                  (SELECT request_id, count(*) as count
                                   FROM $prayers_table
                                   WHERE request_id = $requestID) p
                                ON r.id = p.request_id
                              WHERE r.id = $requestID
                              LIMIT 1" );
  return $request;
}

function praywithus_get_active_requests() {
  global $wpdb;
  $request_table = $wpdb->prefix . "praywithus_requests";
  $prayers_table = $wpdb->prefix . "praywithus_prayers";
  $requests = $wpdb->get_results( "SELECT r.*, ifnull(p.count, 0) as count
                                   FROM $request_table r
                                     LEFT OUTER JOIN
                                     (SELECT request_id, count(*) as count
                                      FROM $prayers_table
                                      GROUP BY request_id) p
                                     ON r.id = p.request_id
                                   WHERE r.active = 1
                                   ORDER BY r.active DESC, r.id ASC" );
  return $requests;
}

function praywithus_get_requests() {
  global $wpdb;
  $request_table = $wpdb->prefix . "praywithus_requests";
  $prayers_table = $wpdb->prefix . "praywithus_prayers";
  $requests = $wpdb->get_results( "SELECT r.*, ifnull(p.count, 0) as count
                                   FROM $request_table r
                                     LEFT OUTER JOIN
                                     (SELECT request_id, count(*) as count
                                      FROM $prayers_table
                                      GROUP BY request_id) p
                                     ON r.id = p.request_id
                                   ORDER BY r.active DESC, r.id ASC" );
  return $requests;
}

function praywithus_actvitate_request($requestId) {
  praywithus_request_set_active_state($requestId, 1);
}

function praywithus_deactvitate_request($requestId) {
  praywithus_request_set_active_state($requestId, 0);
}
function praywithus_request_set_active_state($requestId, $activeState) {
  global $wpdb;
  $request_table = $wpdb->prefix . "praywithus_requests";
  $wpdb->update($request_table,
                array( 'active' => $activeState ),
                array('id' => $requestId ),
                array('%d'),
                array('%d'));
}


?>