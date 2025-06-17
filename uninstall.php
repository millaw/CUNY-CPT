<?php
// Clean up plugin options and CPTs on uninstall
if (!defined('WP_UNINSTALL_PLUGIN')) { exit(); }

// Remove dynamic CPTs and Office taxonomy options
delete_option('cuny_dynamic_cpts');
