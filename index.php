<?php

	/** @noinspection NullCoalescingOperatorCanBeUsedInspection can't be used on PHP 5.6 */

	namespace SpiroAB\Isimo;

	if(!isset($config))
	{
		/** @var \PHPDoc\config $config */
		$config = (object) [];
		if(file_exists(__DIR__ . '/config.php'))
		{
			$config = require __DIR__ . '/config.php';
		}
	}

	if(!isset($url_token))
	{
		/** @var string $url_token */
		$url_token = preg_replace('#.*/#', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	}

	if(!isset($config->token) || empty($url_token) || $url_token !== $config->token)
	{
		header('HTTP/1.1 404 Not found');
		exit;
	}

	header('Content-type: application/json');

	/** @var \PHPDoc\report $data */
	$data = (object) [];
	$data->report = [];
	$data->time = time();
	// TODO: Get this dynamically
	$data->client = 'isimo-standalone v1.0.5';

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

		/** @var string $dbname */
		$dbname = $config->dbname;
		/** @var string $dbuser */
		$dbuser = isset($config->dbuser) ? $config->dbuser : 'root';
		/** @var string $dbpass */
		$dbpass = isset($config->dbpass) ? $config->dbpass : NULL;
		/** @var string $dbhost */
		$dbhost = isset($config->dbhost) ? $config->dbhost : 'localhost';

		try
		{
			/** @noinspection PhpFullyQualifiedNameUsageInspection no include of root scope */
			$db = new \mysqli($dbhost, $dbuser, $dbpass);
			$db->select_db($dbname);
			$result = $db->query('SHOW VARIABLES');
			while($row = $result->fetch_row())
			{
				/** @var string[] $row */
				$data->mysql[$row[0]] = $row[1];
			}
			$result->close();
		}
			/** @noinspection PhpFullyQualifiedNameUsageInspection no include of root scope */
		catch(\Exception $e)
		{
			$data->error[] = $e->getMessage();
		}
	}

	//<editor-fold desc="Git">
	$data->gitsha = NULL;
	/** @var string[] $git_dirs */
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
		/** @var string $git_head */
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
		/** @var string $git_ref */
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
	//</editor-fold>

	//<editor-fold desc="Composer">
	/** @var string[] $composer_dirs */
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
		$data->composer_lock = file_get_contents($composer_dir . '/composer.lock');
		$data->composer_json = file_get_contents($composer_dir . '/composer.json');

		if(empty($config->version_package_name))
		{
			break;
		}

		/** @var \PHPDoc\composerLock $composer_lock */
		$composer_lock = json_decode($data->composer_lock);
		if(empty($composer_lock->packages) || !is_array($composer_lock->packages))
		{
			break;
		}
		foreach($composer_lock->packages as $composer_package)
		{
			if(empty($composer_package->name))
			{
				continue;
			}
			if(empty($composer_package->version))
			{
				continue;
			}
			if($composer_package->name !== $config->version_package_name)
			{
				continue;
			}
			$data->version = $composer_package->version;
		}
		break;
	}
	//</editor-fold>

	if(isset($version))
	{
		/** @var string version */
		$data->version = $version;
	}
	else if(file_exists(__DIR__ . '/version.php'))
	{
		$data->version = require __DIR__ . '/version.php';
	}

	if($data->version === '0.0.0')
	{
		unset($data->version);
	}

	echo json_encode($data, 128), PHP_EOL;
