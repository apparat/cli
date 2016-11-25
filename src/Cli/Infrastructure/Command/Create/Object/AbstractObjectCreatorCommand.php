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
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Abstract object creator command
 *
 * @package Apparat\Cli
 * @subpackage Apparat\Cli\Infrastructure
 */
abstract class AbstractObjectCreatorCommand extends Command
{
    /**
     * Don't validate a question result
     *
     * @var int
     */
    const VALIDATE_NONE = 0;
    /**
     * Question result must not be empty
     *
     * @var int
     */
    const VALIDATE_NOT_EMPTY = 1;
    /**
     * Dialog answers
     *
     * @var array
     */
    protected $answers = [];

    /**
     * Configures the current command
     */
    protected function configure()
    {
        parent::configure();

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
     * Executes the current command
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @return void
     * @see http://symfony.com/doc/current/components/console/helpers/questionhelper.html
     * @see https://github.com/symfony/symfony/issues/19678
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Author
        $this->askFor(
            $input,
            $output,
            'author',
            'Who are you?',
            [self::VALIDATE_NOT_EMPTY => 'The author must not be empty!'],
            getenv('APPARAT_CLI_DEFAULT_AUTHOR')
        );

        // Categories
        $this->askFor(
            $input,
            $output,
            'categories',
            'Which categories apply?',
            [],
            null,
            ['eins', 'zwei', 'drei']
        );

        //ArticleProperties::LOCATION => [ArticleProperties::COLLECTION, ArticleProperties::LOCATION],
        //ArticleProperties::NAME => MetaProperties::PROPERTY_TITLE,
        //ArticleProperties::SUMMARY => MetaProperties::PROPERTY_ABSTRACT,
        //ArticleProperties::CONTENT => ObjectInterface::PROPERTY_PAYLOAD,
    }

    /**
     * Ask a question
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @param string $name Dialog question name
     * @param string $message Question message
     * @param array $validate Validation checks and messages
     * @param string|null $default Default value
     * @param array $autocomplete Autocomplete values
     * @return void
     */
    protected function askFor(
        InputInterface $input,
        OutputInterface $output,
        $name,
        $message,
        array $validate =
        [self::VALIDATE_NONE => null],
        $default = null,
        array $autocomplete = []
    ) {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new Question($message.(empty($default) ? ' ' : " [$default] "), $default);

        // Add autocomplete value if given
        if (count($autocomplete)) {
            $question->setAutocompleterValues($autocomplete);
        }

        // Run through all given validators
        foreach ($validate as $validator => $error) {
            switch ($validator) {
                case self::VALIDATE_NOT_EMPTY:
                    $question->setValidator(
                        function ($answer) use ($error) {
                            if (!strlen(trim($answer))) {
                                throw new \RuntimeException($error);
                            }
                            return $answer;
                        }
                    );
                    break;
            }
        }

        // Ask the user
        $this->answers[$name] = $helper->ask($input, $output, $question);
    }

    /**
     * Return an answer that has been given to a question
     *
     * @param string $name Answer name
     * @return mixed Answer content
     */
    public function getAnswer($name) {
        if (!array_key_exists($name, $this->answers)) {

        }
        return $this->answers[$name];
    }
}
