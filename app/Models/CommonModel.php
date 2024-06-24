<?php namespace ci4commonmodel\Models;

class CommonModel
{
    protected $db;

    public function __construct(string $group = 'default')
    {
        $this->db = \Config\Database::connect($group);
    }

    /**
     * @param string $table
     * @param string $select
     * @param array $where
     * @param string $order
     * @param int $limit
     * @param int $pkCount
     * @param array $like
     * @param array $orWhere
     * @return object
     */
    public function lists(string $table, string $select = '*', array $where = [], string $order = 'id ASC', int $limit = 0, int $pkCount = 0, array $like = [],array $orWhere=[]): array
    {
        $builder = $this->db->table($table);
        $builder->select($select)->where($where);
        if($orWhere) $builder->orWhere($orWhere);
        if (!empty($like)) {
            if (count($like) === 1) {
                $builder->like(key($like), reset($like));
            } else {
                $builder->groupStart();
                foreach ($like as $field => $value) {
                    $builder->orLike($field, $value);
                }
                $builder->groupEnd();
            }
        }
        $builder->orderBy($order);
        if ($limit >= 0 || $pkCount >= 0) $builder->limit($limit, $pkCount);
        return $builder->get()->getResult();
    }

    /**
     * @param string $table
     * @param array $data
     * @return int
     */
    public function create(string $table, array $data = []): int
    {
        $builder = $this->db->table($table);
        $builder->insert($data);
        return $this->db->insertID();
    }

    /**
     * @param string $table
     * @param array $data
     * @return mixed
     */
    public function createMany(string $table, array $data): mixed
    {
        $builder = $this->db->table($table);
        return $builder->insertBatch($data);
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool
     */
    public function edit(string $table, array $data = [], array $where = []): bool
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->update($data);
    }

    /**
     * @param string $table
     * @param array $where
     * @return bool
     */
    public function remove(string $table, array $where = []): bool
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->delete();
    }

    /**
     * @param string $table
     * @param array $where
     * @param string $select
     * @param string $order
     * @return object
     */
    public function selectOne(string $table, array $where = [], string $select = '*',string $order = 'id ASC'): mixed
    {
        $builder = $this->db->table($table);
        return $builder->select($select)->where($where)->orderBy($order)->get()->getRow();
    }

    /**
     * @param string $att
     * @param string $table
     * @param array $where
     * @return int
     */
    public function whereInCheckData(string $att, string $table, array $where = []): int
    {
        $builder = $this->db->table($table);
        return $builder->whereIn($att, $where, 1)->get()->getNumRows();

    }

    /**
     * @param string $table
     * @param array $where
     * @return int
     */
    public function isHave(string $table, array $where): int
    {
        $builder = $this->db->table($table);
        return $builder->getWhere($where, 1)->getNumRows();
    }

    /**
     * @param string $table
     * @param array $where
     * @return int
     */
    public function count(string $table, array $where = []): int
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->countAllResults();
    }

    /**
     * @param string $table
     * @param array $like
     * @param string $select
     * @param array $where
     * @return object
     */
    public function research(string $table, array $like = [], string $select = '*', array $where = []): object
    {
        $builder = $this->db->table($table);
        return $builder->select($select)->where($where)->like($like)->get()->getResult();
    }

    /**
     * @param string $table
     * @param string $select
     * @param array $joins
     * @param string $whereInKey
     * @param array $whereInData
     * @param string $orderBy
     * @return object
    */
    public function notWhereInList(string $table, string $select = '*', array $joins = [], string $whereInKey, array $whereInData, string $orderBy = 'queue ASC'): object
    {
        $builder = $this->db->table($table)->select($select);
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $builder->join($join['table'], $join['cond'], $join['type']);
            }
        }
        $builder->whereNotIn($whereInKey, $whereInData)->orderBy($orderBy);
        return $builder->get()->getResult();
    }

    /**
     * @param string $table
     * @param string $select
     * @param array $joins
     * @param array $where
     * @param string $order
     * @param integer $limit
     * @param integer $pkCount
     * @param array $like
     * @param array $orWhere
     * @return object
     */
    public function whereWithJoins(string $table, string $select = '*',array $joins = [], array $where = [], string $order = 'id ASC', int $limit = 0, int $pkCount = 0, array $like = [],array $orWhere=[]): object
    {
        $builder = $this->db->table($table);
        $builder->select($select);
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $builder->join($join['table'], $join['cond'], $join['type']);
            }
        }
        $builder->where($where);
        if($orWhere) $builder->orWhere($orWhere);
        if (!empty($like)) {
            if (count($like) === 1) {
                $builder->like(key($like), reset($like));
            } else {
                $builder->groupStart();
                foreach ($like as $field => $value) {
                    $builder->orLike($field, $value);
                }
                $builder->groupEnd();
            }
        }
        $builder->orderBy($order);
        if ($limit >= 0 || $pkCount >= 0) $builder->limit($limit, $pkCount);
        return $builder->get()->getResult();
    }
}