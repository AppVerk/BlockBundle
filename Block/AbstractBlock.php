<?php

namespace AppVerk\BlockBundle\Block;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractBlock
{
    /**
     * @var EngineInterface
     */
    private $twigEngine;

    /**
     * @required
     */
    public function setTwigEngine(EngineInterface $twigEngine)
    {
        $this->twigEngine = $twigEngine;
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'BlockBundle:Block:sample.html.twig'
        ]);

        $resolver->addAllowedTypes('template', 'string');
    }

    protected function getSettings(array $options = [])
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        return $optionsResolver->resolve($options);
    }

    private function createResponse()
    {
        return new Response();
    }

    protected function renderResponse(string $template, array $parameters = [])
    {
        $template = $this->twigEngine->render($template, $parameters);

        $response = $this->createResponse();
        $response->setContent($template);

        return $response;
    }

    public function execute(array $options = [])
    {
        $settings = $this->getSettings($options);

        return $this->renderResponse($settings['template'], [
            'settings' => $settings
        ]);
    }
}
