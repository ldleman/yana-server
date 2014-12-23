<?php

require_once('Widget.class.php');

$table = new Widget();
$table->drop();


Section::remove('dashboard');

?>