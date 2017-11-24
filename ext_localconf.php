<?php

defined('TYPO3_MODE') or die('Access denied.');

$extensionConfiguration = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['page_forwarding']);

// Add general page TS settings

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('TCAdefaults.tx_urlforwarding_domain_model_redirect.pid = ' . ($extensionConfiguration['storagePid'] ?? '0'));

// Overwrite classes

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\PatrickBroens\UrlForwarding\Controller\ForwardController::class] = array(
    'className' => \Plan2net\PageForwarding\Controller\ForwardController::class
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\PatrickBroens\UrlForwarding\Hook\TceMain::class] = array(
    'className' => \Plan2net\PageForwarding\Hook\TceMain::class
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\PatrickBroens\UrlForwarding\Domain\Repository\RedirectRepository::class] = array(
    'className' => \Plan2net\PageForwarding\Domain\Repository\RedirectRepository::class
);

// Modify TCA on the fly

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord']
[Plan2net\PageForwarding\Backend\TcaShowitemManipulation::class] = [
    'depends' => [
        \TYPO3\CMS\Backend\Form\FormDataProvider\TcaColumnsProcessShowitem::class,
    ]
];

// Remove this unnecessary page icon stuff

unset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay']['url_forwarding']);

foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks'] as $key => $hookIdentifier) {
    if ($hookIdentifier == \PatrickBroens\UrlForwarding\Hook\CmsLayout::class . '->addRedirectToPageTitle') {
        unset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks'][$key]);
    }
}

