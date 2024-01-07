<?php

namespace App;

use App\Common\Infrastructure\Messenger\CommandBus\AsCommandHandler;
use App\Common\Infrastructure\Messenger\EventBus\AsEventSubscriber;
use App\Feed\Infrastructure\Framework\CompilerPass\FeedProviderSourceShouldBeUniqueCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerAttributeForAutoconfiguration(AsCommandHandler::class, static function (ChildDefinition $definition, AsCommandHandler $attribute, \ReflectionClass|\ReflectionMethod $reflector): void {
            $tagAttributes = get_object_vars($attribute);
            $tagAttributes['bus'] = 'command.bus';

            if ($reflector instanceof \ReflectionMethod) {
                if (isset($tagAttributes['method'])) {
                    throw new LogicException(sprintf('AsCommandHandler attribute cannot declare a method on "%s::%s()".', $reflector->class, $reflector->name));
                }
                $tagAttributes['method'] = $reflector->getName();
            }

            $definition->addTag('messenger.message_handler', $tagAttributes);
        });

        $container->registerAttributeForAutoconfiguration(AsEventSubscriber::class, static function (ChildDefinition $definition, AsEventSubscriber $attribute, \ReflectionClass|\ReflectionMethod $reflector): void {
            $tagAttributes = get_object_vars($attribute);
            $tagAttributes['bus'] = 'event.bus';

            if ($reflector instanceof \ReflectionMethod) {
                if (isset($tagAttributes['method'])) {
                    throw new LogicException(sprintf('AsEventSubscriber attribute cannot declare a method on "%s::%s()".', $reflector->class, $reflector->name));
                }
                $tagAttributes['method'] = $reflector->getName();
            }

            $definition->addTag('messenger.message_handler', $tagAttributes);
        });

        // @todo move to bundle
        $container->addCompilerPass(new FeedProviderSourceShouldBeUniqueCompilerPass());
    }
}
