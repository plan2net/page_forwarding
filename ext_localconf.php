<?php

defined('TYPO3_MODE') or die('Access denied.');

$extensionConfiguration = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['page_forwarding']);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('TCAdefaults.tx_urlforwarding_domain_model_redirect.pid = ' . ($extensionConfiguration['storagePid'] ?? '0'));

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\PatrickBroens\UrlForwarding\Hook\TceMain::class] = array(
    'className' => \Plan2net\PageForwarding\Hook\TceMain::class
);

// Remove this unnecessary page icon stuff
unset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay']['url_forwarding']);

foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks'] as $key => $hookIdentifier) {
    if ($hookIdentifier == \PatrickBroens\UrlForwarding\Hook\CmsLayout::class . '->addRedirectToPageTitle') {
        unset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks'][$key]);
    }
}
