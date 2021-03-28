<?php
extract($_SERVER);
/**
 * @var string $DATABASE_URL
 */
?>
<?= '<' . '?php' ?> return [
    'dbs' => [
        'default' => [
            'adapter'      => \Theia\Component\Db\Adapter\Mysql::class,
            'port'         => <?=parse_url($DATABASE_URL, PHP_URL_PORT)?>,
            'connectQuery' => 'SET NAMES UTF8 COLLATE utf8_unicode_ci',
        ],
        'connections' => [
            'generator' => [
                'host'       => '<?=parse_url($DATABASE_URL, PHP_URL_HOST)?>',
                'database'   => '<?=substr(parse_url($DATABASE_URL, PHP_URL_PATH), 1)?>',
                'user'       => '<?=parse_url($DATABASE_URL, PHP_URL_USER)?>',
                'password'   => '<?=parse_url($DATABASE_URL, PHP_URL_PASS)?>',
            ],
        ],
    ],
    'jira' => [
        'api'  => 'https://jira.local.ru/rest/api/2/',
        'user' => 'govorun',
        'pass' => '6bq3uXrdE3T6',
    ],
];
