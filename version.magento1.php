<?php

	/** @noinspection PhpIncludeInspection Exists on Magento projects */
	require dirname(__DIR__) . '/app/Mage.php';

	/** @noinspection PhpUndefinedClassInspection Exists on Magento projects */
	return Mage::getVersion();
