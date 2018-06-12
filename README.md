# BlockBundle

Symofny Block Bundle


## Configure

Require the bundle with composer:

    $ composer require app-verk/block-bundle

Enable the bundle in the kernel:

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new AppVerk\BlockBundle\BlockBundle(),
            // ...
        );
    }
    
## Twig helper

Render a block:

    {{ render_block(block_id) }}
    
Render a block with options:

    {{ render_block(block_id, {
        'template': 'BlockBundle:Block:sample.html.twig'
    }) }}
    
## Create Block:

Remember to extends AppVerk\BlockBundle\Block\AbstractBlock.

    <?php
    
    namespace AppBundle\Block;
    
    use AppVerk\BlockBundle\Block\AbstractBlock;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
    class HelloBlock extends AbstractBlock
    {
        protected function configureOptions(OptionsResolver $resolver)
        {
            parent::configureOptions($resolver);
    
            $resolver->setDefaults([
                'template' => 'AppBundle:Block:hello.html.twig',
                'message'  => null
            ]);
        }
    }
    
Block should be set as public service or have alias.

    services:
        _defaults:
            autowire: true
            autoconfigure: true
            public: false
    
        AppBundle\:
            resource: '../../*'
            exclude: '../../{Entity,Repository,Tests,Doctrine,Twig}'
    
        AppBundle\Block\HelloBlock:
            public: true

Execute in Twig:

    {{ render_block('AppBundle\\Block\\HelloBlock', {
        'template': 'AppBundle:Block:hello.html.twig',
        'message': 'Hello there!'
    }) }}
    
More complicated Blocks can override execute method and used for example EntityManager:

    <?php
    
    namespace AppBundle\Block;
    
    use AppBundle\Entity\Product;
    use AppVerk\BlockBundle\Block\AbstractBlock;
    use Doctrine\ORM\EntityManagerInterface;
    use Doctrine\ORM\EntityNotFoundException;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
    class ProductBlock extends AbstractBlock
    {
        /**
         * @var EntityManagerInterface
         */
        private $entityManager;
    
    
        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }
    
        protected function configureOptions(OptionsResolver $resolver)
        {
            parent::configureOptions($resolver);
    
            $resolver->setDefaults([
                'template' => 'AppBundle:Block:product.html.twig',
                'slug'     => ''
            ]);
        }
    
        public function execute(array $options = [])
        {
            $settings = $this->getSettings($options);
    
            $entity = $this->entityManager->getRepository(Product::class)->findOneBy([
                'slug' => $settings['slug']
            ]);
            if (!$entity) {
                throw new EntityNotFoundException();
            }
    
            return $this->renderResponse($settings['template'], [
                'settings' => $settings,
                'entity'   => $entity
            ]);
        }
    }


## License

The bundle is released under the [MIT License](LICENSE).
