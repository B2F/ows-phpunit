<?php

function ows_autoloader($class) {
  include __DIR__ .  '/../classes/' . $class . '.class.php';
}

spl_autoload_register('ows_autoloader');

