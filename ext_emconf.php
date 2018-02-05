<?php

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Page forwarding',
	'description' => 'Manage URL redirects in pages',
	'category' => 'backend',
	'version' => '1.0.7',
	'state' => 'stable',
	'uploadfolder' => false,
	'createDirs' => '',
	'clearcacheonload' => true,
	'author' => 'Wolfgang Klinger',
	'author_email' => 'wk@plan2.net',
	'author_company' => 'plan2net GmbH',
	'constraints' => 
	array (
		'depends' => 
		array (
			'typo3' => '8.0.0-8.7.99',
            'url_forwarding' => '1.3.1',
		),
		'suggests' => 
		array (
		),
		'conflicts' => 
		array (
		),
	),
);

