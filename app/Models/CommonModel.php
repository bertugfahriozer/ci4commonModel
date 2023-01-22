<?php namespace ci4commonmodel\Models;

class CommonModel
{
    protected $db;

    public function __construct(string $group = 'default')
    {
        $this->db = \Config\Database::connect($group);
    }

    public function lists(string $table, string $select = '*', array $where = [])
    {
        $builder = $this->db->table($table);
        return $builder->select($select)->where($where)->get()->getResult();
    }

    public function create(string $table, array $data = [])
    {
        $builder = $this->db->table($table);
        $builder->insert($data);
        return $builder->insertID();
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

    public function selectOne(string $table, array $where = [], string $select = '*')
    {
        $builder = $this->db->table($table);
        return $builder->select($select)->where($where)->get()->getRow();
    }

    public function whereInCheckData($att, array $where = [], string $table)
    {
        $builder = $this->db->table($table);
        return $builder->whereIn($att, $where, 1)->get()->getNumRows();

    }

    public function isHave(string $table, $where)
    {
        $builder = $this->db->table($table);
        return $builder->getWhere($where, 1)->getNumRows();
    }

    public function count(string $table, array $where = [])
    {
        $builder = $this->db->table($table);
        return $builder->where($where)->countAllResults();
    }

    public function research(array $like = [], string $table, string $select = '*', array $where = [])
    {
        $builder = $this->db->table($table);
        return $builder->select($select)->where($where)->like($like)->get()->getResult();
    }
}