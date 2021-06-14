<?php declare(strict_types=1);

namespace SasShortcode\Content\Cms\DataResolver;

use SasShortcode\SasShortcode;
use Shopware\Core\Framework\Adapter\Twig\TemplateFinder;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelDefinitionInstanceRegistry;
use Shopware\Core\System\SalesChannel\Exception\SalesChannelRepositoryNotFoundException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Twig\Environment;

class ShortCodeResolver
{
    public const SUPPORT_SHORTCODES = [
        'product' => [
            'associations' => [],
            'view' => '@Storefront/storefront/component/shortcode/product.html.twig',
        ],
        'category' => [
            'associations' => ['media'],
            'view' => '@Storefront/storefront/component/shortcode/category.html.twig',
        ],
    ];

    private SalesChannelDefinitionInstanceRegistry $salesChannelDefinitionRegistry;

    private DefinitionInstanceRegistry $definitionRegistry;

    private Environment $twig;

    private TemplateFinder $templateFinder;

    public function __construct(
        SalesChannelDefinitionInstanceRegistry $salesChannelDefinitionRegistry,
        DefinitionInstanceRegistry $definitionRegistry,
        TemplateFinder $templateFinder,
        Environment $twig
    ) {
        $this->salesChannelDefinitionRegistry = $salesChannelDefinitionRegistry;
        $this->definitionRegistry = $definitionRegistry;
        $this->templateFinder = $templateFinder;
        $this->twig = $twig;
    }

    public function resolveShortcodes(SalesChannelContext $salesChannelContext, string $content): ?string
    {
        $content = preg_replace_callback(
            SasShortcode::PATTERN_ALLOWED,
            function ($matches) use ($salesChannelContext) {
                try {
                    return $this->resolveShortcode($salesChannelContext, $matches['property']);
                } catch (\InvalidArgumentException $e) {
                    return $matches[0];
                }
            },
            $content
        );

        return $content;
    }

    private function resolveShortcode(SalesChannelContext $salesChannelContext, string $path)
    {
        $parts = \explode('=', $path);

        if (\count($parts) < 2) {
            return null;
        }

        $entityName = $parts[0];
        $ids = explode(',', $parts[1]);

        if (!\in_array($entityName, \array_keys(self::SUPPORT_SHORTCODES), true)) {
            return null;
        }

        $context = $salesChannelContext->getContext();

        try {
            $repository = $this->salesChannelDefinitionRegistry->getSalesChannelRepository($entityName);
            $context = $salesChannelContext;
        } catch (SalesChannelRepositoryNotFoundException $exception) {
            $repository = $this->definitionRegistry->getRepository($entityName);
        }

        $criteria = new Criteria(\array_filter($ids));
        $criteria->addAssociations(self::SUPPORT_SHORTCODES[$entityName]['associations']);
        $entity = $repository->search($criteria, $context)->getEntities();

        $view = $this->templateFinder->find(self::SUPPORT_SHORTCODES[$entityName]['view'], false, null);

        return $this->twig->render($view, [
            $entityName => $entity,
        ]);
    }
}
