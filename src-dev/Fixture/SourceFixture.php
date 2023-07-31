<?php

namespace Dev\Fixture;

use App\Feed\Domain\Source\Source;
use App\Feed\Domain\Source\SourceId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class SourceFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // stitcher.io
        $manager->persist(new Source(
            new SourceId('0c65cd65-bf4e-4bd1-ac1d-ccf707e19361'),
            'stitcher.io',
        ));

        // martinfowler.com
        $manager->persist(new Source(
            new SourceId('f2f38859-ba40-4252-90c5-a956056a1dae'),
            'martinfowler.com',
        ));

        $manager->flush();
    }
}
