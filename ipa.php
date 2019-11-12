<?php
for ($i = 0; $i < 60; $i++ ){
	//签名逻辑
	exec("cd /usr/local/homeroot/ipa && /usr/local/php7.2/bin/php artisan command:ipa",$out);
	sleep(1) ;
}
