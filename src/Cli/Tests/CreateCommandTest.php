<?php

/**
 * apparat-cli
 *
 * @category    Apparat
 * @package     Apparat\Cli
 * @subpackage  Apparat\Cli\Tests
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace Apparat\Cli\Tests;

use Apparat\Cli\Infrastructure\Command\Create\Repository\FileCommmand;
use Apparat\Dev\Tests\AbstractTest;
use Apparat\Kernel\Ports\Kernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Create command tests
 *
 * @package Apparat\Cli
 * @subpackage Apparat\Cli\Tests\Fixture
 */
class CreateCommandTest extends AbstractTest
{
    /**
     * Test the creation of a repository using the file adapter strategy
     */
    public function testCreateFileRepositoryCommand()
    {
        $tmpDir = $this->registerTemporaryDirectory(__DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'.repo');

        /** @var Application $application */
        $application = Kernel::create(Application::class);
        $application->add(Kernel::create(FileCommmand::class));
        $command = $application->find('create:repository:file');
        $commandTester = Kernel::create(CommandTester::class, [$command]);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'root' => __DIR__.DIRECTORY_SEPARATOR.'Fixture',
        ));
        $output = $commandTester->getDisplay();
        $this->assertEquals(0, strncmp($output, 'Repository successfully created at', 18));
        $this->assertFileExists($tmpDir.DIRECTORY_SEPARATOR.'size.txt');
    }
}
