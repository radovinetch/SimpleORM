<?php


namespace SimpleORM\tests\model;

use phpDocumentor\Reflection\Utils;
use SimpleORM\tests\SimpleORMTestCase;

class ModelTest extends SimpleORMTestCase
{
    public function testClear()
    {
        User::insert(['user' => mt_rand(9999, 999999)]);
        $this->assertNotEmpty(User::all()->get());
        User::clear();
        $this->assertEmpty(User::all()->get());
    }

    public function testInsert()
    {
        $this->assertInstanceOf(User::class, User::insert(['user' => '345345']));
    }

    public function testSelectNull()
    {
        $user = User::where(['user' => '225464568'])->get();
        $this->assertEquals(null, $user);
    }

    public function testSelectOne()
    {
        $this->assertInstanceOf(User::class, User::where(['id' => 2]));
    }

    public function testSelectMany()
    {
        $this->assertNotEmpty(User::where(['user' => '345345']), '');
    }

    public function testUpdate()
    {
        $user = User::where(['id' => 1])->get();
        $user->setVar('user', $user->getVar('user') . '_');
        $user->save();

        $this->assertEquals($user->getVar('user'), User::where(['id' => 1])->get()->getVar('user'));
    }

    public function testUpdateViaMethod()
    {
        $user = User::where(['id' => 1])->get();
        $array = $user->jsonSerialize();
        $user->update(
            [
                'user' => mt_rand(1, 1000000)
            ]
        );
        $user2 = User::where(['id' => 1])->get();
        $this->assertNotEquals($array, $user2->jsonSerialize());
    }

    public function testSelectWithOrder()
    {
        $users = User::where([])->orderBy(['id' => 'DESC'])->get();

        $this->assertIsArray($users);
    }

    public function testSelectWithOrderWithNonExistField()
    {
        $query = User::where([])->orderBy(['id' => 'DESC', 'nonExists' => 'ASC'])->getBuilder()->getQuery();
        $this->assertEquals("SELECT * FROM `users` ORDER BY id DESC", $query);
    }

    public function testSelectWithOrderErrorValue()
    {
        $query = User::where([])->orderBy(['id' => 'DESC', 'username' => 'AScc'])->getBuilder()->getQuery();
        $this->assertEquals("SELECT * FROM `users` ORDER BY id DESC", $query);
    }

    public function testHasOne()
    {
        /** @var User $user */
        $user = User::where(['id' => 1])->get();
        $password = $user->hasOne(UserPassword::class, 'id', 'id');
        $this->assertEquals('3213232131', $password->getVar('password'));
    }

    public function testBuilder()
    {
        $builder = User::getQueryBuilder();
        $builder->select(['user'])->limit(1);
        $user = User::useBuilder($builder)->get();
        $this->assertInstanceOf(User::class, $user);
    }

    public function testCountAll()
    {
        $this->assertEquals(6, User::countAll());
    }
}