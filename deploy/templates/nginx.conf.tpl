<?php
extract($_SERVER);
/**
 * @var string $APP_HOST
 * @var string $APP_PATH
 * @var bool $APP_ENV_PROD
 * @var int $APP_PORT
 * @var bool $APP_SSL
 */
$APP_SSL = filter_var($APP_SSL, FILTER_VALIDATE_BOOLEAN);
?>
<?if($APP_SSL):?>
server {
    listen 80;
    server_name <?=$APP_HOST?>;
    return 301 https://<?=$APP_HOST?>$request_uri;
}
<?endif?>

server {
    listen       <?=$APP_PORT?><?if($APP_SSL):?> ssl<?endif?>;
    server_name  <?=$APP_HOST?>;

    root   <?=$APP_PATH?>/public;

<?if($APP_SSL):?>
    ssl_certificate <?=$APP_PATH?>/deploy/<?=$APP_HOST?>.crt;
    ssl_certificate_key <?=$APP_PATH?>/deploy/<?=$APP_HOST?>.key;
<?endif?>

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass         unix:/run/php/php7.2-fpm.sock;
        fastcgi_buffers      256 512k;
        fastcgi_buffer_size  512k;
        fastcgi_index        index.php;
        fastcgi_read_timeout 1000;
        include              fastcgi_params;
        fastcgi_param        SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param        PATH_INFO       $fastcgi_path_info;
    }
}