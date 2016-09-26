<?php
$mem = new Memcache;
$mem->connect("localhost", 11211);
echo $mem->get("c0f4cc35a21bc912d73894fb121b1a62");

?>