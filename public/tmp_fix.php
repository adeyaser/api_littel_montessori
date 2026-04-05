<?php
$f = 'C:\xampphp7\htdocs\little_home_api\application\controllers\WaliMurid.php';
$content = file_get_contents($f);
$content = str_replace("new Key(\$this->jwt_secret_key, 'HS256')", "\$this->jwt_secret_key, ['HS256']", $content);
file_put_contents($f, $content);
