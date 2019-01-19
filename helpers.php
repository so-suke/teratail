<?php

if (!function_exists('_dd')) {
  function _dd(...$args) {
    header("HTTP/1.0 500 Internal Server Error");
    call_user_func_array('dd', $args);
  }
}