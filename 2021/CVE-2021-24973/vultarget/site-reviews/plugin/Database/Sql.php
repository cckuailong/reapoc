<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

trait Sql
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var \wpdb
     */
    public $db;

    /**
     * @param string $clause
     * @return array
     */
    public function clauses($clause, array $values = [])
    {
        $prefix = Str::restrictTo('and,join', $clause);
        foreach ($this->args as $key => $value) {
            $method = Helper::buildMethodName($key, 'clause-'.$prefix);
            if (!method_exists($this, $method) || Helper::isEmpty($value)) {
                continue;
            }
            if ($statement = call_user_func([$this, $method])) {
                $values[$key] = $statement;
            }
        }
        return $values;
    }

    /**
     * @return string
     */
    public function escFieldsForInsert(array $fields)
    {
        return sprintf('(`%s`)', implode('`,`', $fields));
    }

    /**
     * @return string
     */
    public function escValuesForInsert(array $values)
    {
        $values = array_values(array_map('esc_sql', $values));
        return sprintf("('%s')", implode("','", $values));
    }

    /**
     * @param string $statement
     * @return string
     */
    public function sql($statement)
    {
        $handle = $this->sqlHandle();
        $statement = glsr()->filterString('database/sql/'.$handle, $statement);
        glsr()->action('database/sql/'.$handle, $statement);
        glsr()->action('database/sql', $statement, $handle);
        return $statement;
    }

    /**
     * @return string
     */
    public function sqlJoin()
    {
        $join = $this->clauses('join');
        $join = glsr()->filterArrayUnique('query/sql/join', $join, $this->sqlHandle(), $this);
        return implode(' ', $join);
    }

    /**
     * @return string
     */
    public function sqlLimit()
    {
        $limit = Helper::ifTrue($this->args['per_page'] > 0,
            $this->db->prepare('LIMIT %d', $this->args['per_page'])
        );
        return glsr()->filterString('query/sql/limit', $limit, $this->sqlHandle(), $this);
    }

    /**
     * @return string
     */
    public function sqlOffset()
    {
        $offsetBy = (($this->args['page'] - 1) * $this->args['per_page']) + $this->args['offset'];
        $offset = Helper::ifTrue($offsetBy > 0,
            $this->db->prepare('OFFSET %d', $offsetBy)
        );
        return glsr()->filterString('query/sql/offset', $offset, $this->sqlHandle(), $this);
    }

    /**
     * @return string|void
     */
    public function sqlOrderBy()
    {
        $values = [
            'random' => 'RAND()',
        ];
        $order = $this->args['order'];
        $orderby = $this->args['orderby'];
        $orderedby = [];
        if (Str::startsWith(['p.', 'r.'], $orderby)) {
            $orderedby[] = "r.is_pinned {$order}";
            $orderedby[] = "{$orderby} {$order}";
        } elseif (array_key_exists($orderby, $values)) {
            $orderedby[] = $values[$orderby];
        }
        $orderedby = glsr()->filterArrayUnique('query/sql/order-by', $orderedby, $this->sqlHandle(), $this);
        if (!empty($orderedby)) {
            return 'ORDER BY '.implode(', ', $orderedby);
        }
    }

    /**
     * @return string
     */
    public function sqlWhere()
    {
        $and = $this->clauses('and');
        $and = glsr()->filterArrayUnique('query/sql/and', $and, $this->sqlHandle(), $this);
        $and = $this->normalizeAndClauses($and);
        return 'WHERE 1=1 '.implode(' ', $and);
    }

    /**
     * @return string
     */
    public function table($table)
    {
        return glsr(SqlSchema::class)->table($table);
    }

    /**
     * @return string
     */
    protected function clauseAndAssignedPosts()
    {
        return $this->clauseIfValueNotEmpty('(apt.post_id IN (%s) AND apt.is_published = 1)', $this->args['assigned_posts']);
    }

    /**
     * @return string
     */
    protected function clauseAndAssignedTerms()
    {
        return $this->clauseIfValueNotEmpty('(att.term_id IN (%s))', $this->args['assigned_terms']);
    }

    /**
     * @return string
     */
    protected function clauseAndAssignedUsers()
    {
        return $this->clauseIfValueNotEmpty('(aut.user_id IN (%s))', $this->args['assigned_users']);
    }

    /**
     * @return string
     */
    protected function clauseAndDate()
    {
        $clauses = [];
        $date = $this->args['date'];
        if (!empty($date['after'])) {
            $clauses[] = $this->db->prepare("(p.post_date >{$date['inclusive']} %s)", $date['after']);
        }
        if (!empty($date['before'])) {
            $clauses[] = $this->db->prepare("(p.post_date <{$date['inclusive']} %s)", $date['before']);
        }
        if (!empty($date['year'])) {
            $clauses[] = $this->db->prepare('(YEAR(p.post_date) = %d AND MONTH(p.post_date) = %d AND DAYOFMONTH(p.post_date) = %d)',
                $date['year'], $date['month'], $date['day']
            );
        }
        if ($clauses = implode(' AND ', $clauses)) {
            return sprintf('AND (%s)', $clauses);
        }
        return '';
    }

    /**
     * @return string
     */
    protected function clauseAndEmail()
    {
        return $this->clauseIfValueNotEmpty('AND r.email = %s', $this->args['email']);
    }

    /**
     * @return string
     */
    protected function clauseAndIpAddress()
    {
        return $this->clauseIfValueNotEmpty('AND r.ip_address = %s', $this->args['ip_address']);
    }

    /**
     * @return string
     */
    protected function clauseAndPostIn()
    {
        return $this->clauseIfValueNotEmpty('AND r.review_id IN (%s)', $this->args['post__in']);
    }

    /**
     * @return string
     */
    protected function clauseAndPostNotIn()
    {
        return $this->clauseIfValueNotEmpty('AND r.review_id NOT IN (%s)', $this->args['post__not_in']);
    }

    /**
     * @return string
     */
    protected function clauseAndRating()
    {
        $column = $this->isCustomRatingField() ? 'pm.meta_value' : 'r.rating';
        return Helper::ifTrue($this->args['rating'] > 0,
            $this->db->prepare("AND {$column} > %d", --$this->args['rating'])
        );
    }

    /**
     * @return string
     */
    protected function clauseAndRatingField()
    {
        return Helper::ifTrue($this->isCustomRatingField(),
            $this->db->prepare("AND pm.meta_key = %s", sprintf('_custom_%s', $this->args['rating_field']))
        );
    }

    /**
     * @return string
     */
    protected function clauseAndStatus()
    {
        return $this->clauseIfValueNotEmpty('AND r.is_approved = %d', $this->args['status']);
    }

    /**
     * @return string
     */
    protected function clauseAndTerms()
    {
        if (Helper::isEmpty($this->args['terms'])) {
            return '';
        }
        $value = Cast::toInt(Cast::toBool($this->args['terms']));
        return $this->clauseIfValueNotEmpty('AND r.terms = %d', $value);
    }

    /**
     * @return string
     */
    protected function clauseAndType()
    {
        return $this->clauseIfValueNotEmpty('AND r.type = %s', $this->args['type']);
    }

    /**
     * @return string
     */
    protected function clauseAndUserIn()
    {
        return $this->clauseIfValueNotEmpty('AND p.post_author IN (%s)', $this->args['user__in']);
    }

    /**
     * @return string
     */
    protected function clauseAndUserNotIn()
    {
        return $this->clauseIfValueNotEmpty('AND p.post_author NOT IN (%s)', $this->args['user__not_in']);
    }

    /**
     * @param string $clause
     * @param array|int|string $value
     * @param bool $prepare
     * @return string
     */
    protected function clauseIfValueNotEmpty($clause, $value, $prepare = true)
    {
        if (Helper::isEmpty($value)) {
            return '';
        }
        if (!$prepare) {
            return $clause;
        }
        if (is_array($value)) {
            $value = implode(',', Arr::uniqueInt($value));
            return sprintf($clause, $value); // this clause uses IN(%s) so we need to bypass db->prepare
        }
        return $this->db->prepare($clause, $value);
    }

    /**
     * @return string
     */
    protected function clauseJoinAssignedPosts()
    {
        return $this->clauseIfValueNotEmpty(
            "{$this->joinMethod()} {$this->table('assigned_posts')} AS apt ON r.ID = apt.rating_id",
            $this->args['assigned_posts'],
            $prepare = false
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinAssignedTerms()
    {
        return $this->clauseIfValueNotEmpty(
            "{$this->joinMethod()} {$this->table('assigned_terms')} AS att ON r.ID = att.rating_id",
            $this->args['assigned_terms'],
            $prepare = false
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinAssignedUsers()
    {
        return $this->clauseIfValueNotEmpty(
            "{$this->joinMethod()} {$this->table('assigned_users')} AS aut ON r.ID = aut.rating_id",
            $this->args['assigned_users'],
            $prepare = false
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinDate()
    {
        return $this->clauseIfValueNotEmpty(
            "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID",
            array_filter($this->args['date']),
            $prepare = false
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinUserIn()
    {
        return $this->clauseIfValueNotEmpty(
            "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID",
            $this->args['user__in'],
            $prepare = false
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinUserNotIn()
    {
        return $this->clauseIfValueNotEmpty(
            "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID",
            $this->args['user__not_in'],
            $prepare = false
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinOrderBy()
    {
        return Helper::ifTrue(Str::startsWith('p.', $this->args['orderby']),
            "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID"
        );
    }

    /**
     * @return string
     */
    protected function clauseJoinRatingField()
    {
        return Helper::ifTrue($this->isCustomRatingField(), 
            "INNER JOIN {$this->db->postmeta} AS pm ON r.review_id = pm.post_id"
        );
    }

    /**
     * @return bool
     */
    protected function isCustomRatingField()
    {
        return 'rating' !== $this->args['rating_field'] && !empty($this->args['rating_field']);
    }

    /**
     * Used to determine the join method used in review assignments
     * @return string
     */
    protected function joinMethod()
    {
        $joins = ['loose' => 'LEFT JOIN', 'strict' => 'INNER JOIN'];
        return Arr::get($joins, glsr_get_option('reviews.assignment', 'strict'), 'INNER JOIN');
    }

    /**
     * @return array
     */
    protected function normalizeAndClauses(array $and)
    {
        $clauses = [];
        foreach ($and as $key => $value) {
            if (Str::startsWith('assigned_', $key)) {
                $clauses[] = $value;
                unset($and[$key]);
            }
        }
        $operator = glsr()->filterString('query/sql/clause/operator', 'OR', $clauses, $this->args);
        $operator = strtoupper($operator);
        $operator = Helper::ifTrue(in_array($operator, ['AND', 'OR']), $operator, 'OR');
        if ($clauses = implode(" {$operator} ", $clauses)) {
            $and['assigned'] = "AND ($clauses)";
        }
        return $and;
    }

    /**
     * @return string
     */
    protected function ratingColumn()
    {
        return Helper::ifTrue($this->isCustomRatingField(), 'pm.meta_value', 'r.rating');
    }

    /**
     * @param int $depth
     * @return string
     */
    protected function sqlHandle($depth = 2)
    {
        return Str::dashCase(Arr::get((new \Exception())->getTrace(), $depth.'.function'));
    }
}
