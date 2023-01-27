<?php namespace ci4commonmodel\Models;

class CommonModel
{
    protected $db;

    public function __construct(string $group = 'default')
    {
        $this->db = \Config\Database::connect($group);
    }

    public function lists(string $table, string $select = '*', array $where = [], $order = 'id ASC',$limit=0,$pkCount=0)
    {
        $builder = $this->db->table($table);
        $builder->select($select)->where($where)->orderBy($order);
        if($limit>=0 || $pkCount>=0) $builder->limit($limit,$pkCount);
        return $builder->get()->getResult();
    }

    public function create(string $table, array $data = [])
    {
        $builder = $this->db->table($table);
        $builder->insert($data);
        return $this->db->insertID();
    }

    public function createMany(string $table, array $data)
    {
        $builder = $this->db->table($table);
        return $builder->insertBatch($data);
    }

    public function edit(string $table, array $data = [], array $where = [])
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->update($data);
    }

    public function remove(string $table, array $where = [])
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->delete();
    }

    public function selectOne(string $table, array $where = [], string $select = '*', $order='id ASC')
    {
        $builder = $this->db->table($table);
        return $builder->select($select)->where($where)->orderBy($order)->get()->getRow();
    }

    public function whereInCheckData(string $att, string $table, array $where = [])
    {
        $builder = $this->db->table($table);
        return $builder->whereIn($att, $where, 1)->get()->getNumRows();

    }

    public function isHave(string $table, array $where)
    {
        $builder = $this->db->table($table);
        return $builder->getWhere($where, 1)->getNumRows();
    }

    public function count(string $table, array $where = [])
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->countAllResults();
    }

    public function research(string $table, array $like = [], string $select = '*', array $where = [])
    {
        $builder = $this->db->table($table);
        return $builder->select($select)->where($where)->like($like)->get()->getResult();
    }
}