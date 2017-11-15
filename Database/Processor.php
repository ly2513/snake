<?php
/**
 * User: yongli
 * Date: 17/11/14
 * Time: 16:08
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
namespace Database;

class Processor
{
    /**
     * Process the results of a "select" query.
     *
     * @param Builder $query
     * @param         $results
     *
     * @return mixed
     */
    public function processSelect(Builder $query, $results)
    {
        return $results;
    }
    
    /**
     * Process an  "insert get ID" query.
     *
     * @param Builder $query
     * @param         $sql
     * @param         $values
     * @param null    $sequence
     *
     * @return int
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        $query->getConnection()->insert($sql, $values);
        $id = $query->getConnection()->getPdo()->lastInsertId($sequence);

        return is_numeric($id) ? (int)$id : $id;
    }

    /**
     * Process the results of a column listing query.
     *
     * @param  array $results
     *
     * @return array
     */
    public function processColumnListing($results)
    {
        return $results;
    }
}