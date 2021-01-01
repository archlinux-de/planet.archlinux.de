<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Feed;
use App\Entity\Item;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    /** @var int */
    private const SEED = 1;

    /** @var int */
    private const MAX_FEED_COUNT = 100;

    /** @var int */
    private const MAX_ITEM_COUNT = 100;

    /** @var string */
    private const MAX_DATETIME = '2021-01-01';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('de_DE');
        $faker->seed(self::SEED);

        for ($feedCount = 0; $feedCount < $faker->numberBetween(1, self::MAX_FEED_COUNT); $feedCount++) {
            $feed = (new Feed($faker->unique()->url))
                ->setLink($faker->unique()->url)
                ->setDescription($faker->sentence)
                ->setLastModified($faker->dateTime(self::MAX_DATETIME))
                ->setTitle($faker->sentence(3));

            for ($itemCount = 0; $itemCount < $faker->numberBetween(0, self::MAX_ITEM_COUNT); $itemCount++) {
                $author = (new Author())
                    ->setName($faker->name)
                    ->setUri($faker->url);

                $item = (new Item($faker->unique()->url))
                    ->setTitle($faker->sentence)
                    ->setLastModified($faker->dateTime(self::MAX_DATETIME))
                    ->setDescription($faker->randomHtml())
                    ->setAuthor($author);

                $feed->addItem($item);
            }

            $manager->persist($feed);
        }

        $manager->flush();
    }
}
