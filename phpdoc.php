<?php

	namespace PHPDoc {
		/**
		 * Class config
		 * @package PHPDoc
		 *
		 * @property string|null composerdir use this composerdir insted of default one
		 * @property string|null dbhost database host
		 * @property string|null dbname database databasename
		 * @property string|null dbpass database password
		 * @property string|null dbuser database username
		 * @property string|null gitdir use this gitdir insted of default one
		 * @property string software report application as this software name
		 * @property string|null token validate request against this token
		 * @property string|null version_package_name take version from this composer package
		 */
		class config extends pretendObject {}

		/**
		 * Class report
		 * @package PHPDoc
		 *
		 * @property string client
		 * @property string composer_json
		 * @property string composer_lock
		 * @property string[] error
		 * @property string gitsha
		 * @property string[] mysql
		 * @property string phpinfo
		 * @property array report
		 * @property string software
		 * @property int time
		 * @property string version
		 */
		class report extends pretendObject {}

		/**
		 * Class composerLock
		 * @package PHPDoc
		 *
		 * @property string content-hash
		 * @property composerPackage[] packages
		 * @property array aliases
		 * @property string minimum-stability
		 * @property object stability-flags
		 * @property bool prefer-stable
		 * @property bool prefer-lowest
		 * @property object platform
		 * @property object platform-dev
		 * @property object platform-overrides
		 */
		class composerLock extends pretendObject {}

		/**
		 * Class composerPackage
		 * @package PHPDoc
		 *
		 * @property string|null name
		 * @property string|null version
		 * @property object source
		 * @property object dist
		 * @property object require
		 * @property object require-dev
		 * @property object suggest
		 * @property string type
		 * @property object autoload
		 * @property string notification-url
		 * @property string[] license
		 * @property object[] authors
		 * @property string description
		 * @property string homepage
		 * @property string time
		 */
		class composerPackage extends pretendObject {}

		class pretendObject {
			public function __get($name)
			{
			}

			public function __set($name, $value)
			{
			}

			public function __isset($name)
			{
				return true;
			}
		}
	}
