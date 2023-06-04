<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\MessageHandlingService;

class MatchHandler extends AbstractHandler
{
    public const DEFAULT_MATCH_SLUG = '$default_match$';

    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        $nodeValue = $this->getNodeValue($nodeName, $data);

        $preparedValues = [];
        foreach ($handlerValue as $key => $value) {
            if (str_starts_with($key, '@')) {
                $key = substr($key, 1);
                $key = $this->getNodeValue($key, $data);
            }
            $preparedValues[$key] = $value;
        }
        $pseudoParams = $preparedValues[$nodeValue] ?? ($preparedValues[self::DEFAULT_MATCH_SLUG] ?? []);

        /** @var MessageHandlingService $handlingService */
        $handlingService = $this->container->get(MessageHandlingService::class);

        return $handlingService->handleSection($data, [$pseudoParams], $context);
    }
}