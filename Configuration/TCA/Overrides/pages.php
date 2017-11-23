<?php

$extensionConfiguration = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['page_forwarding']);

$fields = [
    'tx_pageforwarding_redirects' =>
        [
            'exclude' => 1,
            'label' => 'LLL:EXT:page_forwarding/Resources/Private/Language/locallang.xlf:pages.tx_pageforwarding_redirects',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_urlforwarding_domain_model_redirect',
                'maxitems' => 10,
                'appearance' => [
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                    'showSynchronizationLink' => 0,
                    'showAllLocalizationLink' => 0,
                    'showPossibleLocalizationRecords' => 0,
                    'showRemovedLocalizationRecords' => 0
                ],
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '
                                forward_url,
                                sys_language_uid,
            --div--;LLL:EXT:page_forwarding/Resources/Private/Language/locallang.xlf:tabs.other,
                                internal_page,
                                http_status,
                                --div--;LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:tab.statistics,
                                counter,
                                last_hit
                            '
                        ],
                    ],
                    'columns' => [
                        'sys_language_uid' => [
                            'label' => 'LLL:EXT:page_forwarding/Resources/Private/Language/locallang.xlf:tx_urlforwarding_domain_model_redirect.sys_language_uid',
                        ],
                        'forward_url' => [
                            'label' => 'LLL:EXT:page_forwarding/Resources/Private/Language/locallang.xlf:tx_urlforwarding_domain_model_redirect.forward_url',
                        ],
                        'internal_page' => [
                            'config' => [
                                'type' => 'user',
                                'userFunc' => \Plan2net\PageForwarding\UserFunc\Pages::class . '->getParentId',
                            ]
                        ],
                        'http_status' => [
                            'config' => [
                                'default' => '301',
                                'readOnly' => true
                            ]
                        ]
                    ]
                ]
            ]
        ]
];
// Add new fields to pages:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $fields);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', 'tx_pageforwarding_redirects', implode(',',
    [
        (string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_DEFAULT,
        (string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SHORTCUT

    ]), 'after:title');
