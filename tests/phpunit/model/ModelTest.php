<?php


namespace SimpleORM\tests\model;

use SimpleORM\tests\SimpleORMTestCase;

class ModelTest extends SimpleORMTestCase
{
    public function testInsert()
    {
        $this->assertInstanceOf(User::class, User::insert(['user' => '345345']));
    }

    public function testSelectNull()
    {
        $this->assertEquals(null, User::where(['user' => '228']));
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
}