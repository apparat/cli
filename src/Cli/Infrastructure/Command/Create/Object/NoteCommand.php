<?php

/**
 * apparat-cli
 *
 * @category    Apparat
 * @package     Apparat\Cli
 * @subpackage  Apparat\Cli\Infrastructure
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

namespace Apparat\Cli\Infrastructure\Command\Create\Object;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * CLI command for creating a note object
 *
 * @package Apparat\Cli
 * @subpackage Apparat\Cli\Infrastructure
 */
class NoteCommand extends Command
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        // Set the name of the command (the part after "php bin/apparat")
        $this->setName('create:object:note');

        // Set the short description shown when running "php bin/apparat list"
        $this->setDescription('Create a note object');

        // Full command description when run with "--help"
        $this->setHelp('This command lets you create a new note object inside a repository.');

        // Add the repository root argument
        $defaultRepositoryRoot = getenv('APPARAT_CLI_DEFAULT_REPOSITORY_ROOT');
        $this->addArgument(
            'root',
            empty($defaultRepositoryRoot) ? InputArgument::REQUIRED : InputArgument::OPTIONAL,
            'The root directory of the repository',
            empty($defaultRepositoryRoot) ? null : $defaultRepositoryRoot
        );
    }

    /**
     * Create the file based repository
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = $input->getArgument('root');

        // Ask for the author
        $defaultAuthor = getenv('APPARAT_CLI_DEFAULT_AUTHOR');
        $helper = $this->getHelper('question');
        $question = new Question('Who are you?'.(empty($defaultAuthor) ? '' : " [$defaultAuthor]").' ');
        $question->setValidator(function ($answer) {
            if (!strlen(trim($answer))) {
                throw new \RuntimeException(
                    'The author must not be empty!'
                );
            }
            return $answer;
        });
        $author = $helper->ask($input, $output, $question);

        echo $author;

//        $repository = RepositoryFacade::create(
//            '',
//            [
//                'type' => FileAdapterStrategy::TYPE,
//                'root' => $root
//            ]
//        );
//        $output->writeln(
//            ($repository instanceof RepositoryFacade) ?
//                'Repository successfully created at '.realpath($root) :
//                'Repository creation failed'
//        );
    }
}
