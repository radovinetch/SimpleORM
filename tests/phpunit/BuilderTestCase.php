<?php

namespace SimpleORM\tests;

use SimpleORM\sql\Builder;
use PHPUnit\Framework\TestCase;

class BuilderTestCase extends SimpleORMTestCase
{
    private Builder $builder;

    protected function setUp(): void
    {
        $this->builder = new Builder('users');
    }

    public function testBuildSelectAndWhere()
    {
        $this->builder->select()->where(['name' => 'user', 'password' => 'foobar']);
        $this->assertEquals(
            "SELECT * FROM `users` WHERE `name` = ? AND `password` = ?",
            $this->builder->getQuery()
        );
    }

    public function testBuilderUpdate()
    {
        $this->builder->update(['name' => 'user']);
        $this->assertEquals("UPDATE `users` SET `name` = ?", $this->builder->getQuery());
    }

    public function testBuilderUpdateAndWhere()
    {
        $this->builder->update(['name' => 'user'])->where(['id' => 1]);
        $this->assertEquals("UPDATE `users` SET `name` = ? WHERE `id` = ?", $this->builder->getQuery());
    }

    public function testBuilderDelete()
    {
        $this->builder->delete()->where(['id' => 1]);
        $this->assertEquals("DELETE FROM `users` WHERE `id` = ?", $this->builder->getQuery());
    }

    public function testBuilderInsert()
    {
        $this->builder->insert(['id' => 2, 'name' => 'name']);
        $this->assertEquals("INSERT INTO `users` (`id`, `name`) VALUES (?,?)", $this->builder->getQuery());
    }
}
