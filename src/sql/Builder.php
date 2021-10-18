<?php


namespace SimpleORM\sql;


class Builder
{
    /**
     * @var string
     */
    private string $table;

    /**
     * Builder constructor.
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @var string
     */
    private string $query = '';

    /**
     * @var array
     */
    private array $params = [];

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function insert(array $params): Builder
    {
        $this->params = array_merge($this->params, array_values($params));
        $this->query .= "INSERT INTO `".$this->table."` (".implode(', ', array_map(fn($item) => "`".$item."`", array_keys($params))).") VALUES (".substr(str_repeat('?,', count($params)), 0, -1).")";
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function select(array $fields = []): Builder
    {
        $fields_string = empty($fields) ? '*' : implode(',', array_map(fn($field) => ("`$field`"), array_values($fields)));
        $this->query .= "SELECT ".$fields_string." FROM `" . $this->table . "`";
        return $this;
    }

    /**
     * @param string $table
     * @param string $join_key
     * @param string $key
     * @param string $type
     * @return $this
     */
    public function join(string $table, string $key, string $join_key, string $type = ''): Builder
    {
        $this->query .= " $type JOIN $table ON {$this->table}.$key=$table.$join_key";
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function where(array $params): Builder
    {
        $string = '';
        foreach ($params as $k => $v) {
            $string .= ($string != '' ? ' AND' : ' WHERE') . " {$this->table}.`$k` = ?";
            $this->params[] = $v;
        }
        $this->query .= $string;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function update(array $params): Builder
    {
        $strings = [];
        foreach ($params as $k => $v) {
            $strings[] = "`$k`" . ' = ?';
            $this->params[] = $v;
        }
        $this->query .= "UPDATE `" . $this->table . "` SET " . implode(',', $strings);
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function orderBy(array $params): Builder
    {
        $this->query .= " ORDER BY " . implode(', ', array_map(fn($k, $v) => ($k . ' ' . $v), array_keys($params), array_values($params)));
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): Builder
    {
        $this->query .= " LIMIT $limit";
        return $this;
    }

    /**
     * @param int $num
     * @return $this
     */
    public function offset(int $num): Builder
    {
        $this->query .= " OFFSET $num";
        return $this;
    }

    /**
     * @return $this
     */
    public function delete(): Builder
    {
        $this->query .= "DELETE FROM `".$this->table."`";
        return $this;
    }
}