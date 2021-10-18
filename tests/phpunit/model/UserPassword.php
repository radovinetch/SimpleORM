<?php


namespace SimpleORM\tests\model;


use SimpleORM\model\Model;

class UserPassword extends Model
{
    protected static ?string $table = 'user_passwords';
}