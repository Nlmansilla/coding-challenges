<?php

namespace Wc\tests;

use PHPUnit\Framework\TestCase;
use Wc\Options;
use Wc\UserOptions;

class UserOptionsTest extends TestCase
{
    public function testConstructorWithAllParameters(): void
    {
        $modes = [Options::MODE_LINES, Options::MODE_BYTES];
        $files = ['file1.txt', 'file2.txt'];
        $readFrom = Options::FILES_MODE;
        $userOptions = new UserOptions($modes, $files, $readFrom);
        $this->assertEquals($modes, $userOptions->getModes());
        $this->assertEquals($files, $userOptions->getFiles());
        $this->assertEquals($readFrom, $userOptions->getReadFrom());
    }

    public function testGetModesReturnsProvidedModes(): void
    {
        $modes = [Options::MODE_WORDS, Options::MODE_CHARACTERS];
        $userOptions = new UserOptions($modes, ['file.txt']);
        $result = $userOptions->getModes();
        $this->assertEquals($modes, $result);
        $this->assertCount(2, $result);
    }

    public function testGetModesReturnsDefaultWhenEmpty(): void
    {
        $userOptions = new UserOptions([], ['file.txt']);
        $result = $userOptions->getModes();
        $this->assertCount(3, $result);
        $this->assertContains(Options::MODE_LINES, $result);
        $this->assertContains(Options::MODE_WORDS, $result);
        $this->assertContains(Options::MODE_BYTES, $result);
    }

    public function testGetFilesReturnsProvidedFiles(): void
    {
        $files = ['file1.txt', 'file2.txt', 'file3.txt'];
        $userOptions = new UserOptions([Options::MODE_LINES], $files);
        $result = $userOptions->getFiles();
        $this->assertEquals($files, $result);
        $this->assertCount(3, $result);
    }

    public function testGetFilesReturnsStdinWhenEmpty(): void
    {
        $userOptions = new UserOptions([Options::MODE_LINES], []);
        $result = $userOptions->getFiles();
        $this->assertCount(1, $result);
        $this->assertEquals([STDIN], $result);
    }

    public function testGetReadFromReturnsDefaultValue(): void
    {
        $userOptions = new UserOptions([Options::MODE_LINES], ['file.txt']);
        $result = $userOptions->getReadFrom();
        $this->assertEquals(Options::FILES_MODE, $result);
    }

    public function testGetReadFromReturnsProvidedValue(): void
    {
        $customReadFrom = Options::STDIN_MODE;
        $userOptions = new UserOptions(
            [Options::MODE_LINES],
            ['file.txt'],
            $customReadFrom
        );
        $result = $userOptions->getReadFrom();
        $this->assertEquals($customReadFrom, $result);
    }

    public function testReadonlyClassCannotBeModified(): void
    {
        $modes = [Options::MODE_LINES];
        $files = ['file.txt'];
        $userOptions = new UserOptions($modes, $files);

        $reflection = new \ReflectionClass($userOptions);
        $this->assertTrue($reflection->isReadOnly());
    }

    public function testMultipleCallsReturnSameValues(): void
    {
        $modes = [Options::MODE_BYTES];
        $files = ['test.txt'];
        $userOptions = new UserOptions($modes, $files);
        $firstCallModes = $userOptions->getModes();
        $secondCallModes = $userOptions->getModes();
        $firstCallFiles = $userOptions->getFiles();
        $secondCallFiles = $userOptions->getFiles();

        $this->assertEquals($firstCallModes, $secondCallModes);
        $this->assertEquals($firstCallFiles, $secondCallFiles);
    }

    public function testEmptyModesAndFilesUsesDefaults(): void
    {
        $userOptions = new UserOptions([], []);
        $modes = $userOptions->getModes();
        $files = $userOptions->getFiles();
        $this->assertCount(3, $modes);
        $this->assertEquals([STDIN], $files);
    }

    public function testSingleModeAndFile(): void
    {
        $userOptions = new UserOptions(
            [Options::MODE_LINES],
            ['single.txt']
        );

        $this->assertCount(1, $userOptions->getModes());
        $this->assertCount(1, $userOptions->getFiles());
        $this->assertEquals('single.txt', $userOptions->getFiles()[0]);
    }

    public function testDefaultModesOrder(): void
    {
        $userOptions = new UserOptions([], ['file.txt']);
        $modes = $userOptions->getModes();

        $this->assertEquals(Options::MODE_LINES, $modes[0]);
        $this->assertEquals(Options::MODE_WORDS, $modes[1]);
        $this->assertEquals(Options::MODE_BYTES, $modes[2]);
    }

    /**
     * @dataProvider modesProvider
     */
    public function testDifferentModeCombinations(array $modes, int $expectedCount): void
    {
        $userOptions = new UserOptions($modes, ['file.txt']);
        $result = $userOptions->getModes();
        $this->assertCount($expectedCount, $result);
    }

    public static function modesProvider(): array
    {
        return [
            'single mode' => [[Options::MODE_LINES], 1],
            'two modes' => [[Options::MODE_LINES, Options::MODE_WORDS], 2],
            'three modes' => [[Options::MODE_LINES, Options::MODE_WORDS, Options::MODE_BYTES], 3],
            'empty modes uses default' => [[], 3],
        ];
    }

    /**
     * @dataProvider filesProvider
     */
    public function testDifferentFilesCombinations(array $files, int $expectedCount): void
    {
        $userOptions = new UserOptions([Options::MODE_LINES], $files);
        $result = $userOptions->getFiles();
        $this->assertCount($expectedCount, $result);
    }

    public static function filesProvider(): array
    {
        return [
            'single file' => [['file.txt'], 1],
            'multiple files' => [['file1.txt', 'file2.txt', 'file3.txt'], 3],
            'empty files uses STDIN' => [[], 1],
        ];
    }
}