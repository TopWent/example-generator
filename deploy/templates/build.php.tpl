<?php
extract($_SERVER);
/**
 * @var string $CI_SERVER_URL
 * @var string $CI_PROJECT_TITLE
 * @var string $CI_PROJECT_PATH
 * @var string $CI_BUILD_REF_NAME
 * @var string $GITLAB_USER_NAME
 * @var string $GITLAB_USER_LOGIN
 * @var string $CI_PIPELINE_ID
 */
?>
<?= '<' . '?php' ?> return [
    'gitlab'   => '<?=$CI_SERVER_URL?>',
    'title'    => '<?=$CI_PROJECT_TITLE?>',
    'project'  => '<?=$CI_PROJECT_PATH?>',
    'branch'   => '<?=$CI_BUILD_REF_NAME?>',
    'user'     => '<?=$GITLAB_USER_NAME?>',
    'login'    => '<?=$GITLAB_USER_LOGIN?>',
    'pipeline' => '<?=$CI_PIPELINE_ID?>',
    'datetime' => '<?=date('Y-m-d H:i:s')?>',
];
