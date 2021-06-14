<?php declare(strict_types=1);

namespace SasShortcode;

use Shopware\Core\Framework\Plugin;

class SasShortcode extends Plugin
{
    public const PATTERN_ALLOWED = '/{{\s*(?<property>[\w=\d\,]+)\s*}}/';
}
