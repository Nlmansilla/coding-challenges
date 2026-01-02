<?php

namespace Wc\tests;

use PHPUnit\Framework\TestCase;
use Wc\FileReader;

class FileReaderTest extends TestCase
{
    private string $testFilePath;
    private const string TEST_FILE_PATH = __DIR__ . '/test.txt';
    protected function setUp(): void
    {
        parent::setUp();
        $this->testFilePath = sys_get_temp_dir() . '/test_file_' . uniqid() . '.txt';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }
        parent::tearDown();
    }

    public function testReadSmallFile(): void
    {
        $content = "Hello World\nThis is a test file\n";
        file_put_contents($this->testFilePath, $content);

        $result = '';
        foreach (FileReader::readFile($this->testFilePath) as $chunk) {
            $result .= $chunk;
        }

        $this->assertEquals($content, $result);
    }

    public function testReadRealFile(): void
    {
        $content = file_get_contents(self::TEST_FILE_PATH);
        $result = '';
        foreach (FileReader::readFile(self::TEST_FILE_PATH) as $chunk) {
            $result .= $chunk;
        }

        $this->assertEquals($content, $result);
    }

    public function testReadFileReturnsIterable(): void
    {
        file_put_contents($this->testFilePath, 'test');

        $result = FileReader::readFile($this->testFilePath);

        $this->assertInstanceOf(\Generator::class, $result);
    }
}