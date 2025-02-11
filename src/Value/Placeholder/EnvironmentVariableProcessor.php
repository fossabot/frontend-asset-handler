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

namespace CPSIT\FrontendAssetHandler\Value\Placeholder;

use RuntimeException;
use UnexpectedValueException;

use function sprintf;

/**
 * EnvironmentVariableProcessor.
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-3.0-or-later
 */
final class EnvironmentVariableProcessor implements PlaceholderProcessorInterface
{
    private const REGEX = '/%env\\(([A-Za-z_]+)\\)%/';

    public function canProcess(string $placeholder): bool
    {
        return 1 === preg_match(self::REGEX, $placeholder);
    }

    public function process(string $placeholder): string
    {
        if (1 !== preg_match(self::REGEX, $placeholder, $matches)) {
            throw new UnexpectedValueException('The given placeholder cannot be processed by this processor.', 1628147418);
        }

        $envVariable = $matches[1];
        $replacement = getenv($envVariable);

        if (false === $replacement) {
            $replacement = $_ENV[$envVariable] ?? null;
        }

        if (null === $replacement) {
            throw new UnexpectedValueException(sprintf('The environment variable "%s" is not available.', $envVariable), 1628147471);
        }

        $processedValue = preg_replace(self::REGEX, $replacement, $placeholder);

        // @codeCoverageIgnoreStart
        if (null === $processedValue) {
            throw new RuntimeException(sprintf('An error occurred while replacing "%s".', $envVariable), 1661842097);
        }
        // @codeCoverageIgnoreEnd

        return $processedValue;
    }
}
