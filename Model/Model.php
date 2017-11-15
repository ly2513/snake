<?php
/**
 * User: yongli
 * Date: 17/11/14
 * Time: 17:11
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
namespace Model;

/**
 * Class YP_Model 基类控制器
 *
 * @package YP\Core
 */
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