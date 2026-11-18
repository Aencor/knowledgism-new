<?php
add_filter('http_request_timeout', function($timeout) {
    return 60;
});
add_filter('wpmdb_default_remote_post_timeout', function($timeout) {
    return 60;
});
add_filter('http_request_args', function($r) {
    $r['timeout'] = 60;
    return $r;
});
