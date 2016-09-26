<?php

$mem = new Memcache;
$mem->connect("localhost", 11211);
//$mem->set("test","111", 0,30);
$mem->set("c0f4cc35a21bc912d73894fb121b1a62","", 0,30);


?>