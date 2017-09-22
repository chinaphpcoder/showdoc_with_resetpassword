<?php
return array(
    //邮件服务器
    //邮件配置
    'THINK_EMAIL' => array(
        'SMTP_HOST'   => 'smtp.mxhichina.com', //SMTP服务器
        'SMTP_PORT'   => '465', //SMTP服务器端口
        'SMTP_USER'   => 'admin@shaxiaoseng.com', //SMTP服务器用户名
        'SMTP_PASS'   => 'php@5168', //SMTP服务器密码
        'FROM_EMAIL'  => 'admin@shaxiaoseng.com', //发件人EMAIL
        'FROM_NAME'   => '沙小僧', //发件人名称
        //'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        //'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
     ),
    //'配置项'=>'配置值'
    //使用sqlite数据库
    'DB_TYPE'   => 'Sqlite', 
    'DB_NAME'   => '../Sqlite/showdoc.db.php', 
    //showdoc不再支持mysql http://www.showdoc.cc/help?page_id=31990
    'DB_HOST'   => 'localhost',
    'DB_USER'   => 'showdoc', 
    'DB_PWD'    => 'showdoc123456',
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => '', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集
    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'URL_HTML_SUFFIX' => '',//url伪静态后缀
    'URL_MODEL' => 3 ,//URL兼容模式
    'URL_CASE_INSENSITIVE'=>true,
    'SHOW_ERROR_MSG'        =>  true,    // 显示错误信息，这样在部署模式下也能显示错误
    'STATS_CODE' =>'',  //可选，统计代码
    'TMPL_CACHE_ON' => false,//禁止模板编译缓存
    'HTML_CACHE_ON' => false,//禁止静态缓存
    //上传文件到七牛的配置
    'UPLOAD_SITEIMG_QINIU' => array(
                    'maxSize' => 5 * 1024 * 1024,//文件大小
                    'rootPath' => './',
                    'saveName' => array ('uniqid', ''),
                    'driver' => 'Qiniu',
                    'driverConfig' => array (
                            'secrectKey' => '', 
                            'accessKey' => '',
                            'domain' => '',
                            'bucket' => '', 
                        )
                    ),
);