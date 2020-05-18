<?php

class Select extends Db {

    protected $column = null;
    protected $from = null;
    protected $join = null;
    protected $where = null;
    protected $groupBy = null;
    protected $orderBy = null;
    protected $limit = null;
    protected $db;

    public function select() {
        $this->db = new Db();
        return $this;
    }

    public function column($param = '*') {
        $this->column = $param;
        return $this;
    }

    public function from($param) {
        $this->from = $param;
        return $this;
    }

    public function join($param) {
        $this->join .= ' ' . $param;
        return $this;
    }

    public function where($param) {
        $this->where .= ' ' . $param;
        return $this;
    }

    public function groupBy($param) {
        $this->groupBy = $param;
        return $this;
    }

    public function orderBy($param) {
        $this->orderBy = $param;
        return $this;
    }

    public function limit($q, $p = null) {
        $this->limit = "LIMIT " . (!is_null($q) ? $q . ',' : null) . $p;
        return $this;
    }

    public function query($toSql = null) {

        $sql = "SELECT "
                . (is_null($this->column) ? '*' : $this->column)
                . " FROM " . $this->from
                . " $this->join"
                . " $this->where"
                . " $this->groupBy"
                . " $this->orderBy"
                . " $this->limit";

        if (!is_null($toSql)) {
            return Crud::p($sql, true);
        }

        $this->column = null;
        $this->from = null;
        $this->join = null;
        $this->where = null;

        return $this->db->selectDb($sql);
    }

    public function queryNumRows($toSql = null) {

        $sql = "SELECT "
                . (is_null($this->column) ? '*' : $this->column)
                . " FROM " . $this->from
                . " $this->join "
                . " $this->where ";

        if (!is_null($toSql)) {
            return Crud::p($sql, true);
        }

        $query = $this->sqlNumRows($sql);

        $this->column = null;
        $this->from = null;
        $this->join = null;
        $this->where = null;

        return $query;
    }

}
