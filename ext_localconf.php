<?php

/**
 * Extension configuration
 */

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// Register with "crawler" extension:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['crawler']['procInstructions']['tx_ncstaticfilecache_clearstaticfile'] = 'clear static cache file';

$hookNamespace = 'SFC\\NcStaticfilecache\\Hook\\';

// Hook to process clearing static cached files if "crawler" extension is active:
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['headerNoCache'][$_EXTKEY] = $hookNamespace . 'Crawler->clearStaticFile';

// Log a cache miss if no_cache is true
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][$_EXTKEY] = $hookNamespace . 'LogNoCache->log';

// Create cache
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['insertPageIncache'][$_EXTKEY] = 'SFC\\NcStaticfilecache\\StaticFileCache';

// Set cookie when User logs in
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['initFEuser'][$_EXTKEY] = $hookNamespace . 'InitFrontendUser->setFeUserCookie';

// register command controller
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'SFC\\NcStaticfilecache\\Command\\CacheCommandController';

/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\StaticCacheable', 'check');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\ValidUri', 'check');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\ValidDoktype', 'check');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\NoWorkspacePreview', 'check');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\NoUserOrGroupSet', 'check');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\NoIntScripts', 'check');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\LoginDeniedConfiguration', 'check');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\PageCacheable', 'check');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\NoNoCache', 'check');
$signalSlotDispatcher->connect('SFC\\NcStaticfilecache\\StaticFileCache', 'cacheRule', 'SFC\\NcStaticfilecache\\Cache\\Rule\\Enable', 'check');

// new Cache for Static file caches
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_file_cache'] = array(
	'frontend' => 'SFC\\NcStaticfilecache\\Cache\\UriFrontend',
	'backend'  => 'SFC\\NcStaticfilecache\\Cache\\StaticFileBackend',
	'groups'   => array(
		'pages',
		'all'
	),
);