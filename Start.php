<?php
/**
 * User: yongli
 * Date: 17/11/16
 * Time: 09:27
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
function do_load_class($className)
{

    $className = str_replace('\\', '/', $className);
    $className = str_replace('Snake/', '', $className);
    
    // 加载配置文件
    $path = __DIR__ . DIRECTORY_SEPARATOR . $className . '.php';
    echo '加载文件 ' .$className .' 成功 '. PHP_EOL;
    if (file_exists($path)) {
        require $path;
    }
}

spl_autoload_register('do_load_class');

$db['default'] = [
    'driver'    => 'mysql',             // 数据库驱动
    'host'      => '127.0.0.1',         // 数据库主机
    'port'      => '3306',              // 数据库端口
    'database'  => 'zhubao',            // 数据库名称
    'username'  => 'root',              // 用户名
    'password'  => 'root',              // 密码
    'charset'   => 'utf8',              // 字符编码
    'collation' => 'utf8_general_ci',   // 排序规则
    'prefix'    => '',                  // 表的前缀
];

$capsule       = new Snake\Database\Capsule\Manager;
//
foreach ($db as $key => $dbConfig) {
    if ($key != 'doctrine') {
        $capsule->addConnection($dbConfig, $key);
    }
}
// 设置全局访问的连接
$capsule->setAsGlobal();
$capsule->bootEloquent();
