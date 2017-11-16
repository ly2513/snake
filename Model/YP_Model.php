<?php
/**
 * User: yongli
 * Date: 17/11/16
 * Time: 10:52
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
namespace Snake\Model;

use Snake\Database\Eloquent\Model;
use Snake\Database\Eloquent\SoftDelete;

class YP_Model extends Model
{
    // 开启软删除
    use SoftDelete;
    // 定义软删除字段
    const  DELETED_AT = 'is_delete';

    protected $dates = ['deleted_at'];

    // 处理 Eloquent 的自动维护db 列
    const  CREATED_AT = 'create_time';
    const  UPDATED_AT = 'update_time';

    // 设置create_at/update_at 时间格式为 Unix 时间戳,默认为 DateTime 格式数据
    protected $dateFormat = 'U';
}