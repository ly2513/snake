<?php
/**
 * User: yongli
 * Date: 17/11/14
 * Time: 14:41
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
namespace Database;

abstract class Grammar
{
    /**
     * The grammar table prefix.
     *
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * Get the grammar's table prefix.
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * Set the grammar's table prefix.
     *
     * @param  string $prefix
     *
     * @return $this
     */
    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;

        return $this;
    }

}