<?php

use Faker\Factory;
use GummerD\PHPnew\Test;
use GummerD\PHPnew\Models\User;
use GummerD\PHPnew\Repositories\UserRepo\MemoryRepoUsers;


require_once __DIR__ . "/../vendor/autoload.php";


$faker = Factory::create('ru_Ru');

$userRepo = new MemoryRepoUsers();

for($i = 1; $i<=10; $i++){
    $user = new User($i, $faker->firstName(), $faker->lastName());
    //echo $user . "<br>";
    $userRepo->save($user);
    
}

print_r($userRepo->getAll());
 

$man = new Test();
$man->getName($faker->lastName());