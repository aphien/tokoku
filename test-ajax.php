<?php
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

$_GET['action'] = 'tokoku_search';
$_GET['keyword'] = 'a';
$_GET['nonce'] = wp_create_nonce('tokoku_search_nonce');

tokoku_ajax_search();
