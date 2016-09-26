<?php

$mem = new Memcache;
$mem->connect("localhost", 11211);
echo $mem->get("test");


?>