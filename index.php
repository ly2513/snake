<?php
/**
 * User: yongli
 * Date: 17/11/16
 * Time: 09:28
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
require 'Start.php';
//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_NO_BUILTINS);
//$XHPROF_ROOT = dirname(__DIR__) .DIRECTORY_SEPARATOR . 'xhprof/xhprof_lib/utils/' ;
use Snake\Database\Capsule\Manager as DB;
use Snake\Model\SmsModel;

DB::listen(function ($sql, $bindings, $time) {
    echo $sql . PHP_EOL;
});
//DB::beginTransaction();
//$data = DB::selectOne('select * from zb_sys_sms');
//print_r($data);
$update = [
    'uid'         => 1,
    'business_id' => 100001,
    'phone'       => '18518178485',
    'code'        => '123456',
    'create_time' => time(),
    'is_delete'   => 0,
];
//die;
//try {
//    $data = DB::insert(
//        'INSERT INTO zb_sys_sms (uid,business_id,phone,code,create_time,is_delete) VALUES (?,?,?,?,?,?)',
//        array_values($update)
//    );
//    DB::update('UPDATE zb_retail_member_address SET is_default = 0 WHERE id = ? AND member_id = ? AND is_delete = 0', [1,2]);
//
//    DB::commit();
//} catch (\Exception $e) {
//    DB::rollback();
//}
$data   = SmsModel::select('*')->whereBusinessId(100001)->get()->toArray();
//print_r($data);
die;
$sql    = SmsModel::select('*')->whereBusinessId(100001)->toSql();
$status = SmsModel::whereBusinessId(100001)->delete();
$sql1   = SmsModel::whereBusinessId(100001)->delete();
//echo $sql1 . PHP_EOL;
//print_r($data);
//echo $sql . PHP_EOL;
$sql2 = DB::delete('DELETE FROM zb_sys_sms WHERE id = ? AND uid = ? AND is_delete = 0', [91,1]);
$data1 = DB::table('zb_sys_sms')->select(['id'])->get();
//$sql3 = DB::table('zb_sys_sms')->select(['id'])->toSql();
$sql3 = DB::table('zb_sys_sms')->where('id', 91)->delete();
//echo $sql3 .PHP_EOL;

//print_r($data1);

//$xhprof_data = xhprof_disable();
//include_once $XHPROF_ROOT . "xhprof_lib.php";
//include_once $XHPROF_ROOT . "xhprof_runs.php";
//$xhprof_runs = new \XHProfRuns_Default();
//$run_id      = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");
//$sql = SmsModel::select('*')->whereBusinessId(100001)->toSql();
//$status = SmsModel::insertGetId($update);
//print_r($status);
//print_r($sql);
