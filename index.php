<?php
	$config = (object) [];
	if(file_exists(__DIR__ . '/config.php'))
	{
		$config = require __DIR__ . '/config.php';
	}

	$config_token = '';
	$url_token = preg_replace('#.*/#', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	if(isset($config->token))
	{
		$config_token = $config->token;
	}

	if(!$url_token || $url_token !== $config_token)
	{
		header('HTTP/1.1 403 Wrong token');
		echo 'Invalid token', PHP_EOL, '<style>BODY {color: red;}</style>';
		die();
	}

	header('Content-type: application/json');
	$data = (object) [];
	$data->report = [];
	$data->time = time();
	$data->client = 'isimo-standalone v1.0.0';

	// Place holders
	$data->software = 'php';
	$data->version = '0.0.0';

	// PHP Info
	ob_start();
	phpinfo();
	$data->phpinfo = ob_get_clean();

	if(isset($config->software))
	{
		$data->software = (string) $config->software;
	}

	// mysql
	if(isset($config->dbname))
	{
		$data->mysql = [];

		$dbname = $config->dbname;
		$dbuser = isset($config->dbuser) ? $config->dbuser : 'root';
		$dbpass = isset($config->dbpass) ? $config->dbpass : null;
		$dbhost = isset($config->dbhost) ? $config->dbhost : 'localhost';

		try
		{
			$db = new \mysqli($dbhost, $dbuser, $dbpass);
			$db->select_db($dbname);
			$result = $db->query('SHOW VARIABLES');
			while($row = $result->fetch_row())
			{
				$data->mysql[$row[0]] = $row[1];
			}
			$result->close();
		}
		catch(\Exception $e)
		{
			$data->error[] = $e-getmessage();
		}
	}

	// Git
	$data->gitsha = NULL;
	$git_dirs = [
		'',
		dirname(__DIR__) . '/.git',
		$_SERVER['DOCUMENT_ROOT'] . '/.git',
	];
	if(isset($config->gitdir))
	{
		$git_dirs[0] = $config->gitdir;
	}
	$git_dirs = array_unique(array_filter($git_dirs));
	foreach($git_dirs as $git_dir)
	{
		if(!is_dir($git_dir))
		{
			continue;
		}
		if(!is_file($git_dir . '/HEAD'))
		{
			continue;
		}
		$git_head = file_get_contents($git_dir . '/HEAD');
		if(!$git_head)
		{
			continue;
		}
		$git_head = trim(substr($git_head, 4));
		if(!$git_head)
		{
			continue;
		}
		if(!is_file($git_dir . '/' . $git_head))
		{
			continue;
		}
		$git_ref = file_get_contents($git_dir . '/' . $git_head);
		if(!$git_ref)
		{
			continue;
		}
		$git_ref = trim($git_ref);
		if(!$git_ref)
		{
			continue;
		}
		$data->gitsha = $git_ref;
		break;
	}

	// composer
	$composer_dirs = [
		'',
		dirname(__DIR__) . '/.git',
		$_SERVER['DOCUMENT_ROOT'] . '/.git',
	];
	if(isset($config->composerdir))
	{
		$composer_dirs[0] = $config->composerdir;
	}
	$composer_dirs = array_unique(array_filter($composer_dirs));

	foreach($composer_dirs as $composer_dir)
	{
		if(!file_exists($composer_dir . '/composer.lock'))
		{
			continue;
		}
		$data->composer_lock = file_get_contents($composer_dir . "/composer.lock");
		$composer_lock = json_decode($data->composer_lock);
		if($composer_lock && isset($composer_lock->packages) && is_array($composer_lock->packages))
		{
			foreach($composer_lock->packages as $composer_package)
			{
				if(!isset($composer_package->name)) continue;
				if(!isset($composer_package->version)) continue;
				if($composer_package->name !== 'silverstripe/framework') continue;
				$data->version = $composer_package->version;
			}
		}
		break;
	}

	if(file_exists(__DIR__ . '/version.php')) {
		$data->version = require __DIR__ . '/version.php';
	}

	if($data->version === '0.0.0')
	{
		unset($data->version);
	}

	echo json_encode($data, 128), PHP_EOL;
