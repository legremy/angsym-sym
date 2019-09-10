<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($u = 0; $u < 5; $u++) {
            $user = new User;
            $user->setEmail("user$u@sym.com")
                ->setAvatar("https://robohash.org/user$u@sym.com")
                ->setPassword($this->encoder->encodePassword($user, "password"));
            $manager->persist($user);

            for ($c = 0; $c < mt_rand(5, 15); $c++) {
                $customer = new Customer;
                $customer->setFirstName($faker->firstName)
                    ->setLastName($faker->lastName)
                    ->setEmail($faker->email)
                    ->setUser($user);
                $manager->persist($customer);

                for ($i = 0; $i < mt_rand(1, 3); $i++) {
                    $invoice = new Invoice;
                    $invoice->setAmount($faker->randomFloat(2, 250, 2500))
                        ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                        ->setSentAt($faker->dateTimeBetween("-6 months"))
                        ->setCustomer($customer);
                    $manager->persist($invoice);
                }
            }
        }

        $manager->flush();
    }
}
