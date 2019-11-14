<?php
for ($i = 0; $i < 60; $i++ ){
	//签名逻辑
	exec("cd /usr/local/homeroot/ipa && /usr/bin/php artisan command:ipa",$out);
	sleep(1) ;
}
