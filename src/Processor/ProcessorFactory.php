<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "cpsit/frontend-asset-handler".
 *
 * Copyright (C) 2021 Elias Häußler <e.haeussler@familie-redlich.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace CPSIT\FrontendAssetHandler\Processor;

use CPSIT\FrontendAssetHandler\ChattyInterface;
use CPSIT\FrontendAssetHandler\Exception;
use Symfony\Component\Console;
use Symfony\Component\DependencyInjection;

/**
 * ProcessorFactory.
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-3.0-or-later
 */
final class ProcessorFactory implements ChattyInterface
{
    private ?Console\Output\OutputInterface $output = null;

    public function __construct(
        private readonly DependencyInjection\ServiceLocator $processors,
    ) {
    }

    /**
     * @throws Exception\UnsupportedClassException
     * @throws Exception\UnsupportedTypeException
     */
    public function get(string $type): ProcessorInterface
    {
        if (!$this->has($type)) {
            throw Exception\UnsupportedTypeException::create($type);
        }

        // Fetch processor instance
        $processor = $this->processors->get($type);

        // Validate processor instance
        if (!($processor instanceof ProcessorInterface)) {
            throw Exception\UnsupportedClassException::create($processor::class);
        }

        // Pass output to chatty processor
        if ($processor instanceof ChattyInterface && null !== $this->output) {
            $processor->setOutput($this->output);
        }

        return $processor;
    }

    public function has(string $type): bool
    {
        return $this->processors->has($type);
    }

    public function setOutput(Console\Output\OutputInterface $output): void
    {
        $this->output = $output;
    }
}
