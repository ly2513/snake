<?php
/**
 * User: yongli
 * Date: 17/11/16
 * Time: 09:28
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
require 'Start.php';

use Snake\Database\Capsule\Manager as DB;
use Snake\Model\SmsModel;

$data = DB::selectOne('select * from zb_sys_sms');
print_r($data);
$update = [
    'uid'=>1,
    'business_id'=>100001,
    'phone'=>'18518178485',
    'code'=>'123456',
    'create_time'=>time(),
    'is_delete'=>0,
];
//$data = DB::insert(
//'INSERT INTO zb_sys_sms (uid,business_id,phone,code,create_time,is_delete) VALUES (?,?,?,?,?,?)',
//                array_values($update));
$data = SmsModel::select('*')->whereBusinessId(100001)->get()->toArray();
print_r($data);
//$sql = SmsModel::select('*')->whereBusinessId(100001)->toSql();
//$status = SmsModel::insertGetId($update);
//print_r($status);
//print_r($sql);