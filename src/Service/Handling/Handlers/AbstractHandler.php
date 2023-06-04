<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractHandler
{
    use ContainerAwareTrait;

    /**
     * @param mixed $handlerValue
     * @param string $nodeName
     * @param array $data
     * @param array $context
     * @return HandlingResultInterface
     */
    public abstract function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface;

    protected function getNodeValue(string $nodeName, array $data): mixed
    {
        $path = explode('.', $nodeName);

        $result = $data;

        foreach ($path as $node) {
            $result = $result[$node] ?? null;
            if (is_null($result)) {
                break;
            }
        }

        return $result;
    }

    protected function setNodeValue(string $nodeName, array &$data, mixed $value)
    {
        $path = explode('.', $nodeName);

        $temp = &$data;
        foreach ($path as $key) {
            if (!isset($temp[$key])) {
                $temp[$key] = [];
            }
            $temp = &$temp[$key];
        }
        $temp = $value;
        unset($temp);
    }

    protected function ifNodeExists(string $nodeName, array $data): bool
    {
        $path = explode('.', $nodeName);

        $result = $data;

        foreach ($path as $node) {
            if (!array_key_exists($node, $result)) {
                return false;
            }
            $result = $result[$node] ?? null;
        }

        return true;
    }
}