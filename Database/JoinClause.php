<?php
/**
 * User: yongli
 * Date: 17/11/14
 * Time: 15:57
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
namespace Database;

class JoinClause extends Builder
{
    /**
     * The type of join being performed.
     *
     * @var string
     */
    public $type;

    /**
     * The table the join clause is joining to.
     *
     * @var string
     */
    public $table;
    
    /**
     * The parent query builder instance.
     *
     * @var Builder
     */
    private $parentQuery;

    /**
     * Create a new join clause instance.
     *
     * JoinClause constructor.
     *
     * @param Builder $parentQuery
     * @param         $type
     * @param         $table
     */
    public function __construct(Builder $parentQuery, $type, $table)
    {
        $this->type        = $type;
        $this->table       = $table;
        $this->parentQuery = $parentQuery;
        parent::__construct($parentQuery->getConnection(), $parentQuery->getGrammar(), $parentQuery->getProcessor());
    }

    /**
     * Add an "on" clause to the join.
     *
     * On clauses can be chained, e.g.
     *
     *  $join->on('contacts.user_id', '=', 'users.id')
     *       ->on('contacts.info_id', '=', 'info.id')
     *
     * will produce the following SQL:
     *
     * on `contacts`.`user_id` = `users`.`id`  and `contacts`.`info_id` = `info`.`id`
     *
     * @param  \Closure|string $first
     * @param  string|null     $operator
     * @param  string|null     $second
     * @param  string          $boolean
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function on($first, $operator = null, $second = null, $boolean = 'and')
    {
        if ($first instanceof \Closure) {
            return $this->whereNested($first, $boolean);
        }

        return $this->whereColumn($first, $operator, $second, $boolean);
    }

    /**
     * Add an "or on" clause to the join.
     *
     * @param      $first
     * @param null $operator
     * @param null $second
     *
     * @return JoinClause
     */
    public function orOn($first, $operator = null, $second = null)
    {
        return $this->on($first, $operator, $second, 'or');
    }

    /**
     * Get a new instance of the join clause builder.
     *
     * @return static
     */
    public function newQuery()
    {
        return new static($this->parentQuery, $this->type, $this->table);
    }
}