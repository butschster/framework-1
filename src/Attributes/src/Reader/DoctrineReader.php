<?php

/**
 * This file is part of Spiral Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Attributes\Reader;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Spiral\Attributes\Exception\InitializationException;
use Spiral\Attributes\Reader\Reader as BaseReader;

final class DoctrineReader extends BaseReader
{
    /**
     * @var Reader|null
     */
    private $reader;

    /**
     * @param Reader|null $reader
     */
    public function __construct(Reader $reader = null)
    {
        $this->checkAvailability();

        $this->reader = $reader ?? new AnnotationReader();
    }

    /**
     * {@inheritDoc}
     */
    public function getClassMetadata(\ReflectionClass $class, string $name = null): iterable
    {
        $result = $this->reader->getClassAnnotations($class);

        return $this->filter($name, $result);
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctionMetadata(\ReflectionFunctionAbstract $function, string $name = null): iterable
    {
        if ($function instanceof \ReflectionMethod) {
            $result = $this->reader->getMethodAnnotations($function);

            return $this->filter($name, $result);
        }

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyMetadata(\ReflectionProperty $property, string $name = null): iterable
    {
        $result = $this->reader->getPropertyAnnotations($property);

        return $this->filter($name, $result);
    }

    /**
     * @return bool
     */
    protected function isAvailable(): bool
    {
        return \interface_exists(Reader::class);
    }

    /**
     * @return void
     */
    private function checkAvailability(): void
    {
        if ($this->isAvailable()) {
            return;
        }

        throw new InitializationException('Requires the "doctrine/annotations" package');
    }

    /**
     * @param string|null $name
     * @param iterable|object[] $annotations
     * @return object[]
     */
    private function filter(?string $name, iterable $annotations): iterable
    {
        if ($name === null) {
            yield from $annotations;

            return;
        }

        foreach ($annotations as $annotation) {
            if ($annotation instanceof $name) {
                yield $annotation;
            }
        }
    }
}
