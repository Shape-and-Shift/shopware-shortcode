<?php declare(strict_types=1);

namespace SasShortcode\Subscriber;

use SasShortcode\Content\Cms\DataResolver\ShortCodeResolver;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Routing\RequestTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseSubscriber implements EventSubscriberInterface
{
    private ShortCodeResolver $shortCodeResolver;

    private SeoUrlPlaceholderHandlerInterface $seoUrlReplacer;

    public function __construct(ShortCodeResolver $shortCodeResolver, SeoUrlPlaceholderHandlerInterface $seoUrlReplacer)
    {
        $this->shortCodeResolver = $shortCodeResolver;
        $this->seoUrlReplacer = $seoUrlReplacer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.response' => 'onContentResponse',
        ];
    }

    public function onContentResponse(ResponseEvent $event): void
    {
        $salesChannelContext = $event->getRequest()->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);

        if (!$salesChannelContext || !$salesChannelContext instanceof SalesChannelContext) {
            return;
        }

        $response = $event->getResponse();
        $content = $this->shortCodeResolver->resolveShortcodes($salesChannelContext, $response->getContent());

        $host = $event->getRequest()->attributes->get(RequestTransformer::STOREFRONT_URL);

        if ($content !== false) {
            $response->setContent(
                $this->seoUrlReplacer->replace($content, $host, $salesChannelContext)
            );
        }
    }
}
