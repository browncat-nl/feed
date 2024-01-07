<?php

namespace App\Feed\Infrastructure\Framework\CompilerPass;

use App\Feed\Application\Service\FeedProvider\FeedProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FeedProviderSourceShouldBeUniqueCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $sources = [];

        $services = $container->findTaggedServiceIds(FeedProvider::class);

        foreach ($services as $service => $attributes) {
            if (class_exists($service) === false) {
                throw new \Exception(sprintf('Couldn\'t derive existing FQCN from service %s, the service is probably aliased', $service));
            }

            if (method_exists($service, 'getSource') === false) {
                throw new \Exception('%s must implement static method `getSource`.');
            }

            $source = call_user_func([$service, 'getSource']);

            if (in_array($source, $sources)) {
                throw new \Exception(sprintf(
                    'The \'source\' on a FeedProvider should be unique. Duplicate source found for %s.',
                    $service,
                ));
            }

            $sources[] = $source;
        }
    }
}
