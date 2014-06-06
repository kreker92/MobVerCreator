<?php
	$casperjs = "/home/k/kreker92/casperjs/bin/casperjs";
	$script = 'add_site.js';
	$arg0 = $_POST['new_site'];
	// $arg0 = 'http://nbilko.com';
	putenv("PHANTOMJS_EXECUTABLE=/home/k/kreker92/phantomjs/bin/phantomjs");
	$command = "$casperjs $script $arg0";
	$result = shell_exec($command);
	// $json_data = json_encode($result);
	echo $result;
	echo $arg0;
?>
