<?php

namespace AppVerk\BlockBundle\Twig;


use AppVerk\BlockBundle\Block\AbstractBlock;
use AppVerk\BlockBundle\Exception\BlockNotSupportedException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BlockExtension extends AbstractExtension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('render_block', [
                $this, 'renderBlock'
            ], [
                'is_safe' => ['html']
            ])
        ];
    }

    public function renderBlock($id, array $parameters = [])
    {
        $block = $this->container->get($id);
        if (!$block instanceof AbstractBlock) {
            throw new BlockNotSupportedException("Can not find block.");
        }

        return $block
            ->execute($parameters)
            ->getContent();
    }
}
