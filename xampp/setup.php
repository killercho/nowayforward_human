<?php
$SERVER = 'localhost';
$PORT = 3306;
$USER = 'root';
$PASSWORD = '';

$REPOSITORY = dirname(__DIR__);
$ARCHIVES_DIR = $REPOSITORY . '/.archives';
$PHP_FPM_SOCKET = '';
$MYSQL_UNIX_SOCKET = '';

echo "Preparing database...";
$conn = new PDO(
	"mysql:host=$SERVER;port=$PORT",
	$USER,
	$PASSWORD
);

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

foreach (glob(__DIR__ . '/../migrations/*.sql') as $migration) {
	$query = file_get_contents($migration);
	$conn->exec($query);
	echo ".";
}

echo "Success";

echo "</br>Preparing Apache...";

$vhost = file_get_contents(__DIR__ . '/../apache/sites/nowayforward_human.conf.tpl');
$vhost = str_replace(8000, 80, $vhost);
preg_match_all('/\${([^}]*)}/', $vhost, $envVars);

foreach ($envVars[1] as $var) {
	$vhost = str_replace("\${{$var}}", $$var, $vhost);
	echo '.';
}

file_put_contents('../../../apache/conf/extra/httpd-vhosts.conf', $vhost);

echo 'Success';

echo '<h1>Setup complete! Restart apache!</h1>';
