<?php


namespace SimpleORM\tests\model;

use SimpleORM\model\Model;

class User extends Model
{
    protected static ?string $table = "users";

    protected static array $fields = ['id', 'user'];

    public function getPassword(): ?Model
    {
        return $this->hasOne(UserPassword::class, 'id', 'id');
    }
}