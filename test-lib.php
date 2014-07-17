<?php

require_once __DIR__.'/uri.lib.php';


$uri = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');

$uri->replace('scheme', 'http');

echo $uri;
