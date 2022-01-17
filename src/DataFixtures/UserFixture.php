<?php

namespace App\DataFixtures;

 use App\Entity\User;
 use App\Enum\UserRoles;
 use Doctrine\Bundle\FixturesBundle\Fixture;
 use Doctrine\Persistence\ObjectManager;
 use Faker\Factory;
 use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

 class UserFixture extends Fixture
 {
     /**
      * @var UserPasswordHasherInterface
      */
     private $encoder;

     /**
      * @var \Faker\Generator
      */
     private $faker;

     public function __construct(UserPasswordHasherInterface $encoder)
     {
         $this->encoder = $encoder;
         $this->faker = Factory::create('de');
     }

     public function load(ObjectManager $manager)
     {
         $user = new User();
         $user->setFirstName($this->faker->firstName());
         $user->setLastName($this->faker->colorName);
         $user->setEmail("admin@admin.com");
         $user->setRoles([UserRoles::ADMIN]);
         $passwordHash = $this->encoder->hashPassword($user, "admin");
         $user->setPassword($passwordHash);
         $manager->persist($user);

         $user = new User();
         $user->setFirstName($this->faker->firstName());
         $user->setLastName($this->faker->colorName);
         $user->setEmail("manager@manager.com");
         $user->setRoles([UserRoles::MANAGER]);
         $passwordHash = $this->encoder->hashPassword($user, "manager");
         $user->setPassword($passwordHash);
         $manager->persist($user);

         $user = new User();
         $user->setFirstName($this->faker->firstName());
         $user->setLastName($this->faker->colorName);
         $user->setEmail("worker@worker.com");
         $user->setRoles([UserRoles::WORKER]);
         $passwordHash = $this->encoder->hashPassword($user, "worker");
         $user->setPassword($passwordHash);
         $manager->persist($user);

         $user = new User();
         $user->setFirstName($this->faker->firstName());
         $user->setLastName($this->faker->colorName);
         $user->setEmail("worker_2@worker_2.com");
         $user->setRoles([UserRoles::WORKER]);
         $passwordHash = $this->encoder->hashPassword($user, "worker_2");
         $user->setPassword($passwordHash);
         $manager->persist($user);

         $manager->flush();
     }
 }