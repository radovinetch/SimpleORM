<?php


namespace SimpleORM\model;

use SimpleORM\connector\connection\Connection;
use SimpleORM\sql\Builder;

abstract class Model implements \JsonSerializable
{
    /**
     * @var string|null
     */
    protected static ?string $table = null;

    /**
     * ORM может работать и без этого, но желательно прописать в каждой модели все её поля.
     * В Builder будет проверка на то, существует ли каждое поле и если поле не найдено в таблице - оно исключается из запроса
     * Все это нужно, чтобы когда данные указываются динамически (например сортировка из $_GET) нельзя было ввести несуществующее поле и получить ошибку
     *
     * @var array
     */
    protected static array $fields = [];

    /**
     * @var string[]
     */
    protected array $not_update_fields = ['id', 'created_at'];

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var Builder|null
     */
    protected ?Builder $builder = null;

    /**
     * @param string $key
     * @param $value
     */
    public function setVar(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getVar(string $key, $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * @return string
     */
    protected static function getTable(): string
    {
        return static::$table ?? strtolower((new \ReflectionClass(static::class))->getShortName());
    }

    /**
     * @var Connection $connection
     */
    protected static Connection $connection;

    /**
     * @param Connection $connection
     */
    public static function setConnection(Connection $connection): void
    {
        self::$connection = $connection;
    }

    /**
     * @return Builder|null
     */
    public function getBuilder(): ?Builder
    {
        return $this->builder;
    }

    /**
     * @param Builder|null $builder
     */
    public function setBuilder(?Builder $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * @return Builder
     */
    public static function getQueryBuilder(): Builder
    {
        return new Builder(self::getTable(), static::$fields);
    }

    /**
     * @param Builder $builder
     * @return Model
     */
    public static function useBuilder(Builder $builder): Model
    {
        $model = new static();
        $model->setBuilder($builder);
        return $model;
    }

    /**
     * @return Model
     */
    public static function all(): Model
    {
        $model = new static();
        $model->setBuilder(self::getQueryBuilder()->select());
        return $model;
    }

    /**
     * Clear table
     */
    public static function clear(): void
    {
        $model = new static();
        $model->setBuilder(self::getQueryBuilder()->delete());
        self::$connection->exec($model->getBuilder()->getQuery());
    }

    /**
     * @return int
     */
    public static function countAll(): int
    {
        $model = new static();
        $model->setBuilder(self::getQueryBuilder()->buildSelect('COUNT(*) as `count`'));
        return $model->get()->getVar('count');
    }

    /**
     * @param array $params
     * @return Model
     */
    public static function where(array $params): Model
    {
        $model = new static();
        $model->setBuilder(self::getQueryBuilder()->select()->where($params));
        return $model;
    }

    /**
     * @param string $k
     * @param $v
     * @return $this
     */
    public function orWhere(string $k, $v): Model
    {
        $builder = $this->getBuilder();
        $this->setBuilder($builder->orWhere($k, $v));
        return $this;
    }

    /**
     * @param array $params
     * @return Model
     */
    public static function insert(array $params): Model
    {
        $model = new static();
        foreach ($params as $k=>$v) {
            $model->setVar($k, $v);
        }
        $builder = self::getQueryBuilder();
        $query = $builder->insert($params)->getQuery();
        $params = $builder->getParams();
        self::$connection->exec($query, $params);
        $model->setVar('id', self::$connection->getPDO()->lastInsertId());
        return $model;
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $builder = self::getQueryBuilder();
        $query = $builder->delete()->where(['id' => $this->getVar('id')])->getQuery();
        $params = $builder->getParams();
        self::$connection->exec($query, $params);
    }

    /**
     * @return void
     */
    public function save(): void
    {
        $builder = self::getQueryBuilder();
        $data = $this->data;
        foreach ($this->not_update_fields as $field) {
            unset($data[$field]);
        }
        $query = $builder->update($data)->where(['id' => $this->data['id']])->getQuery();
        $params = $builder->getParams();
        self::$connection->exec($query, $params);
    }

    /**
     * @return array|Model|null
     */
    public function get(): array|Model|null
    {
        $builder = $this->getBuilder();
        if ($builder == null) {
            return null;
        }

        $params = $builder->getParams();
        $fetch = self::$connection->fetch($builder->getQuery(), $params);
        if (empty($fetch)) {
            return null;
        }

        $array = [];
        foreach ($fetch as $item) {
            $model = new static();
            foreach ($item as $k => $v) {
                $model->setVar($k, $v);
            }
            $array[] = $model;
        }

        return count($array) > 1 ? $array : array_shift($array);
    }

    /**
     * @param $params
     * @return $this
     */
    public function orderBy($params): Model
    {
        $builder = $this->getBuilder();
        $builder->orderBy($params);
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): Model
    {
        $builder = $this->getBuilder();
        $builder->limit($limit);
        return $this;
    }

    /**
     * @param string $class
     * @param string $key
     * @param string $join_key
     * @return Model|null
     */
    public function hasOne(string $class, string $key, string $join_key): ?Model
    {
        /**
         * @var $relation_model Model
         */
        $relation_model = new $class();
        $builder = $relation_model::getQueryBuilder();
        $builder->select()->join(static::getTable(), $key, $join_key, 'INNER')->where(['id' => $this->getVar('id')])->limit(1);
        $relation_model->setBuilder($builder);
        return $relation_model->get();
    }

    /**
     * @param array $array
     */
    public function update(array $array)
    {
        foreach ($array as $key => $value) {
            $this->setVar($key, $value);
        }
        $this->save();
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}