<?php

	namespace PHPDoc {
		/**
		 * Class config
		 * @package PHPDoc
		 *
		 * @property string|null composerdir
		 * @property string|null dbhost
		 * @property string|null dbname
		 * @property string|null dbpass
		 * @property string|null dbuser
		 * @property string|null gitdir
		 * @property string software
		 * @property string|null token
		 * @property string|null version_package_name
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
