<?php

namespace App\DataFixtures;

use App\Entity\Plan;
use App\Enum\PlanStatus;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use DateTime;

class PlanFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserPasswordHasherInterface $encoder, UserRepository $userRepository)
    {
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {

        $users = [
            $this->userRepository->findOneBy(['email' => 'worker@worker.com']),
            $this->userRepository->findOneBy(['email' => 'worker_2@worker_2.com'])];
        $admin = $this->userRepository->findOneBy(['email' => 'admin@admin.com']);

        foreach (range(1, 20) as $index) {
            $firstDay = rand(1, 29);
            $lastDay = $firstDay + rand(1, 29);
            $now = new DateTime();
            $now_copy = new DateTime();

            $plan = new Plan();
            $plan->setAuthor($users[rand(0, 1)]);
            if ($index % 5 == 0) {
                $plan->setResolvedBy($admin);
                if (rand(0,1))
                $plan->setStatus(rand(0,1) ? PlanStatus::APPROVED : PlanStatus::REJECTED);
            } else {
                $plan->setStatus(PlanStatus::PENDING);
            }
            $plan->setVacationStartDate($now->modify("+$firstDay day"));
            $plan->setVacationEndDate($now_copy->modify("+$lastDay day"));
            $manager->persist($plan);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixture::class];
    }

}
