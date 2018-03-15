<?php
declare(strict_types=1);
namespace Helhum\Typo3Console\Tests\Functional\Command;

/*
 * This file is part of the TYPO3 Console project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

use Helhum\Typo3Console\Mvc\Cli\FailedSubProcessCommandException;

class CacheCommandControllerTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function cacheCanBeFlushed()
    {
        $output = $this->executeCoveredConsoleCommand('cache:flush');
        $this->assertSame('Flushed all caches.', $output);
    }

    /**
     * @test
     */
    public function cacheCanBeFlushedWhenNotSetUp()
    {
        $packageStatesFile = getenv('TYPO3_PATH_ROOT') . '/typo3conf/PackageStates.php';
        $localConfFile = getenv('TYPO3_PATH_ROOT') . '/typo3conf/LocalConfiguration.php';
        rename($packageStatesFile, $packageStatesFile . '_');
        rename($localConfFile, $localConfFile . '_');
        try {
            $output = $this->executeCoveredConsoleCommand('cache:flush');
            $this->assertSame('Flushed all file caches.', $output);
        } finally {
            rename($packageStatesFile . '_', $packageStatesFile);
            rename($localConfFile . '_', $localConfFile);
        }
    }

    /**
     * @test
     */
    public function cacheCanBeFlushedAsFilesOnlyWhenNotSetUp()
    {
        $packageStatesFile = getenv('TYPO3_PATH_ROOT') . '/typo3conf/PackageStates.php';
        $localConfFile = getenv('TYPO3_PATH_ROOT') . '/typo3conf/LocalConfiguration.php';
        rename($packageStatesFile, $packageStatesFile . '_');
        rename($localConfFile, $localConfFile . '_');
        try {
            $output = $this->executeCoveredConsoleCommand('cache:flush', ['--files-only']);
            $this->assertSame('Flushed all file caches.', $output);
        } finally {
            rename($packageStatesFile . '_', $packageStatesFile);
            rename($localConfFile . '_', $localConfFile);
        }
    }

    /**
     * @test
     */
    public function cacheCanBeForceFlushedFlushed()
    {
        $output = $this->executeCoveredConsoleCommand('cache:flush', ['--force']);
        $this->assertSame('Force flushed all caches.', $output);
    }

    /**
     * @test
     */
    public function fileCachesCanBeFlushed()
    {
        $output = $this->executeCoveredConsoleCommand('cache:flush', ['--files-only']);
        $this->assertSame('Flushed all file caches.', $output);
    }

    /**
     * @test
     */
    public function fileCachesCanBeForceFlushedFlushed()
    {
        $output = $this->executeCoveredConsoleCommand('cache:flush', ['--files-only', '--force']);
        $this->assertSame('Force flushed all file caches.', $output);
    }

    /**
     * @test
     */
    public function cacheGroupsCanBeFlushed()
    {
        $output = $this->executeCoveredConsoleCommand('cache:flushgroups', ['pages']);
        $this->assertSame('Flushed all caches for group(s): "pages".', $output);
    }

    /**
     * @test
     */
    public function invalidGroupsMakesCommandFail()
    {
        try {
            $this->executeCoveredConsoleCommand('cache:flushgroups', ['foo'], [], null, true);
        } catch (FailedSubProcessCommandException $e) {
            $this->assertSame(1, $e->getExitCode());
            $this->assertContains('Invalid cache groups "foo".', $e->getOutputMessage());
        }
    }

    /**
     * @test
     */
    public function cacheGroupsCanBeListed()
    {
        $output = $this->executeCoveredConsoleCommand('cache:listgroups');
        $this->assertContains('The following cache groups are registered: ', $output);
    }

    /**
     * @test
     */
    public function cacheTagsCanBeFlushed()
    {
        $output = $this->executeCoveredConsoleCommand('cache:flushtags', ['foo']);
        $this->assertSame('Flushed caches by tags "foo".', $output);
    }

    /**
     * @test
     */
    public function cacheTagsAndGroupsCanBeFlushed()
    {
        $output = $this->executeCoveredConsoleCommand('cache:flushtags', ['foo', '--groups' => 'pages']);
        $this->assertSame('Flushed caches by tags "foo" in groups: "pages".', $output);
    }
}
