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

  // NB: Using stripslashes_deep because... ugh, don't get me started.
  //
  // I tried. I really tried. But this is an obvious outgrowth of the
  // PHP fractal of bad design [1].
  //
  //  * There's PHP magic quotes, which automagically escapes incoming data.
  //  * On top of that, $wpdb insert, prepare, and other sensible
  //    default database methods that perform escaping.
  //  * On top of that, WordPress 3.0+ does its own magic-quoting. [2]
  //
  //  Gack. What a morass of gack. Give me any other language, please.
  // 
  // 1: http://me.veekun.com/blog/2012/04/09/php-a-fractal-of-bad-design/
  // 2: http://stackoverflow.com/questions/7341942/wpdb-update-or-wpdb-insert-results-in-slashes-being-added-in-front-of-quotes
  
  $request_table = $wpdb->prefix . "praywithus_requests";
  $rows_affected = $wpdb->query(
                     $wpdb->prepare("INSERT INTO $request_table
                                      (title, description, created_at)
                                      VALUES
                                      (%s, %s, NOW())",
                                    array( stripslashes_deep($title),
                                           stripslashes_deep($description) )));
  return $rows_affected;
}


function praywithus_add_prayer($requestID, $sessionID) {
  global $wpdb;
  $prayers_table = $wpdb->prefix . "praywithus_prayers";
  $rows_affected = $wpdb->query(
                     $wpdb->prepare("INSERT INTO $prayers_table
                                      (request_id, session_id)
                                      VALUES
                                      (%d, %s)",
                                    array( $requestID, stripslashes_deep($sessionID) )));
  return $rows_affected;
}


function praywithus_get_request($requestID) {
  global $wpdb;
  $request_table = $wpdb->prefix . "praywithus_requests";
  $prayers_table = $wpdb->prefix . "praywithus_prayers";
  $request = $wpdb->get_row(
               $wpdb->prepare("SELECT r.*, ifnull(p.count, 0) as count
                               FROM $request_table r
                                 LEFT OUTER JOIN
                                   (SELECT request_id, count(*) as count
                                    FROM $prayers_table
                                    WHERE request_id = %d) p
                                 ON r.id = p.request_id
                               WHERE r.id = %d
                               LIMIT 1",
                              $requestID,
                              $requestID
             ));
  return $request;
}

function praywithus_get_active_requests($sessionID) {
  global $wpdb;
  $request_table = $wpdb->prefix . "praywithus_requests";
  $prayers_table = $wpdb->prefix . "praywithus_prayers";
  $requests = $wpdb->get_results(
                $wpdb->prepare("SELECT r.*, ifnull(p.count, 0) as count, ifnull(s.count, 0) as praying
                                FROM $request_table r
                                  LEFT OUTER JOIN
                                   (SELECT request_id, count(*) as count
                                    FROM $prayers_table
                                    GROUP BY request_id) p
                                   ON r.id = p.request_id
                                   LEFT OUTER JOIN
                                   (SELECT request_id, count(*) as count
                                    FROM $prayers_table
                                    WHERE session_id = %s
                                    GROUP BY request_id) s
                                   ON r.id = s.request_id
                                 WHERE r.active = 1
                                 ORDER BY r.active DESC, r.id ASC",
                               array( stripslashes_deep($sessionID) ))
              );
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

function praywithus_delete_request($requestId) {
  global $wpdb;
  $request_table = $wpdb->prefix . "praywithus_requests";
  $prayers_table = $wpdb->prefix . "praywithus_prayers";
  $wpdb->query($wpdb->prepare("DELETE FROM $prayers_table
                               WHERE request_id = %d",
                              $requestId));
  $wpdb->query($wpdb->prepare("DELETE FROM $request_table
                               WHERE id = %d",
                              $requestId));
}

function count_contents($count) {
  $count = max(0, intval($count) - 1);
  $other = 'other';
  if ( $count != 1 ) {
    $other .= 's';
  }
  if ( $count == 0 ) {
    return "You are praying.";
  } else {
    return "You and $count $other are praying.";
  }
}


?>