<?php

namespace Dev\Fixture;

use App\Common\Domain\Url\Url;
use App\Feed\Domain\Category\Category;
use App\Feed\Domain\Category\CategoryId;
use App\Feed\Domain\Source\Source;
use App\Feed\Domain\Source\SourceId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class SourceFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist($category = new Category(new CategoryId('45abf5f1-8ced-4b24-8c50-343b3e4b3e6d'), 'some-category'));

        // stitcher.io
        $manager->persist(new Source(
            new SourceId('0c65cd65-bf4e-4bd1-ac1d-ccf707e19361'),
            'stitcher.io',
            Url::createFromString('https://stitcher.io/rss'),
            $category,
        ));

        // martinfowler.com
        $manager->persist(new Source(
            new SourceId('f2f38859-ba40-4252-90c5-a956056a1dae'),
            'martinfowler.com',
            Url::createFromString('https://martinfowler.com/feed.atom'),
            $category,
        ));

        $manager->flush();
    }
}
