<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "cpsit/frontend-asset-handler".
 *
 * Copyright (C) 2022 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace CPSIT\FrontendAssetHandler\Helper;

use OndraM\CiDetector\CiDetector;
use Symfony\Component\Process;

/**
 * VcsHelper.
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-3.0-or-later
 */
final class VcsHelper
{
    public static function getCurrentBranch(): ?string
    {
        if (null !== ($branch = self::getCurrentBranchFromEnvironment())) {
            return $branch;
        }
        if (null !== ($branch = self::getCurrentBranchFromCi())) {
            return $branch;
        }

        $process = new Process\Process(['git', 'symbolic-ref', '--short', 'HEAD']);
        $process->run();

        if ($process->isSuccessful()) {
            return trim($process->getIncrementalOutput());
        }

        return null;
    }

    private static function getCurrentBranchFromEnvironment(): ?string
    {
        if ($branch = getenv('FRONTEND_ASSETS_BRANCH')) {
            return $branch;
        }

        return null;
    }

    private static function getCurrentBranchFromCi(): ?string
    {
        $ciDetector = new CiDetector();

        if (!$ciDetector->isCiDetected()) {
            return null;
        }

        return $ciDetector->detect()->getBranch() ?: null;
    }
}
