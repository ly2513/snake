<?php
/**
 * User: yongli
 * Date: 17/11/14
 * Time: 17:00
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */


require 'Start.php';

use Database\Capsule as DB;

$data = DB::select('select * from zb_sys_sms');
print_r($data);