<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'Adminpanel Extended',
	'description' => 'Extended functionality for the admin panel',
	'category' => 'frontend',
	'version' => '0.0.2',
	'state' => 'alpha',
	'clearcacheonload' => 1,
	'author' => 'Susanne Moog',
	'author_email' => '',
	'constraints' => [
		'depends' => [
		    'typo3' => '9.5.0-9.5.99'
        ],
		'conflicts' => [],
		'suggests' => [],
    ]
];