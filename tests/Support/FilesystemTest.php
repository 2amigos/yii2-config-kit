<?php

/*
 * This file is part of the 2amigos/yii2-config-kit project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Config\Tests\Support;

use Da\Config\Support\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    /**
     * @var string
     */
    private $tmpDir;
    /**
     * @var Filesystem
     */
    private $fs;

    protected function setUp()
    {
        $this->tmpDir = __DIR__ . '/tmp';
        $this->fs = new Filesystem();
        $this->fs->makeDirectory($this->tmpDir);
    }

    protected function tearDown()
    {
        $this->fs->deleteDirectory($this->tmpDir);
    }

    public function testGetsTheContentsOfAFile()
    {
        file_put_contents($this->tmpDir . '/file.txt', 'Hello World');
        $this->assertEquals('Hello World', $this->fs->get($this->tmpDir . '/file.txt'));
    }

    public function testGetShared()
    {
        if (!function_exists('pcntl_fork')) {
            $this->markTestSkipped('Skipping since the pcntl extension is not available');
        }
        $content = str_repeat('123456', 1000000);
        $result = 1;
        for ($i = 1; $i <= 20; ++$i) {
            $pid = pcntl_fork();
            if (!$pid) {
                $files = new Filesystem;
                $files->put($this->tmpDir . '/file.txt', $content, true);
                $read = $files->get($this->tmpDir . '/file.txt', true);
                exit(strlen($read) === strlen($content) ? 1 : 0);
            }
        }
        while (pcntl_waitpid(0, $status) !== -1) {
            $status = pcntl_wexitstatus($status);
            $result *= $status;
        }
        $this->assertSame($result, 1);
    }

    public function testReadLines()
    {
        file_put_contents($this->tmpDir . '/file.txt', "Hello\nWorld");
        $lines = $this->fs->readLines($this->tmpDir . '/file.txt');
        $this->assertCount(2, $lines);
    }

    public function testGetsRequiredFileValue()
    {
        file_put_contents($this->tmpDir . '/file.php', '<?php return [];');
        $value = $this->fs->getRequiredFileValue($this->tmpDir . '/file.php');
        $this->assertTrue(is_array($value));
    }

    public function testRequireOnceRequiresFilesProperly()
    {
        mkdir($this->tmpDir . '/foo');
        file_put_contents($this->tmpDir . '/foo/foo.php', '<?php function function_xyz(){};');
        $this->fs->requireOnce($this->tmpDir . '/foo/foo.php');
        file_put_contents($this->tmpDir . '/foo/foo.php', '<?php function function_xyz_changed(){};');
        $this->fs->requireOnce($this->tmpDir . '/foo/foo.php');
        $this->assertTrue(function_exists('function_xyz'));
        $this->assertFalse(function_exists('function_xyz_changed'));
    }

    public function testExists()
    {
        $this->assertTrue($this->fs->exists($this->tmpDir));
        file_put_contents($this->tmpDir . '/file.txt', 'Hello World');
        $this->assertTrue($this->fs->exists($this->tmpDir . '/file.txt'));
    }

    public function testPutContents()
    {
        $this->fs->put($this->tmpDir . '/file.txt', 'Hello World');

        $this->assertStringEqualsFile($this->tmpDir . '/file.txt', 'Hello World');
    }

    public function testPrependContents()
    {
        $this->fs->put($this->tmpDir . '/file.txt', 'World');
        $this->fs->prepend($this->tmpDir . '/file.txt', 'Hello ');
        $this->assertStringEqualsFile($this->tmpDir . '/file.txt', 'Hello World');
    }

    public function testAppendContents()
    {
        $this->fs->put($this->tmpDir . '/file.txt', 'Hello ');
        $this->fs->append($this->tmpDir . '/file.txt', 'World');
        $this->assertStringEqualsFile($this->tmpDir . '/file.txt', 'Hello World');
    }

    public function testDeletesFiles()
    {
        file_put_contents($this->tmpDir . '/file.txt', 'Hello World');
        $this->fs->delete($this->tmpDir . '/file.txt');
        $this->assertFalse($this->fs->exists($this->tmpDir . '/file.txt'));

        file_put_contents($this->tmpDir . '/foo.txt', 'foo');
        file_put_contents($this->tmpDir . '/bar.txt', 'bar');
        $this->fs->delete($this->tmpDir . '/foo.txt', $this->tmpDir . '/bar.txt');
        $this->assertFalse($this->fs->exists($this->tmpDir . '/foo.txt'));
        $this->assertFalse($this->fs->exists($this->tmpDir . '/bar.txt'));

        file_put_contents($this->tmpDir . '/foo.txt', 'foo');
        file_put_contents($this->tmpDir . '/bar.txt', 'bar');
        $this->fs->delete([$this->tmpDir . '/foo.txt', $this->tmpDir . '/bar.txt']);
        $this->assertFalse($this->fs->exists($this->tmpDir . '/foo.txt'));
        $this->assertFalse($this->fs->exists($this->tmpDir . '/bar.txt'));
    }

    public function testMakeDirectory()
    {
        $this->fs->makeDirectory($this->tmpDir . '/test');
        $this->assertTrue($this->fs->isDirectory($this->tmpDir . '/test'));
    }

    public function testDeleteDirectory()
    {
        mkdir($this->tmpDir . '/foo');
        file_put_contents($this->tmpDir . '/foo/file.txt', 'Hello World');

        $this->fs->deleteDirectory($this->tmpDir . '/foo');
        $this->assertFalse(is_dir($this->tmpDir . '/foo'));
        $this->assertFileNotExists($this->tmpDir . '/foo/file.txt');
    }

    public function testGetFileNameSizeAndFileExtension()
    {
        $size = file_put_contents($this->tmpDir . '/file.txt', 'Hello World');
        $this->assertEquals('file', $this->fs->name($this->tmpDir . '/file.txt'));
        $this->assertEquals($size, $this->fs->size($this->tmpDir . '/file.txt'));
        $this->assertEquals('txt', $this->fs->extension($this->tmpDir . '/file.txt'));
    }

    public function testBasenameReturnsBasename()
    {
        file_put_contents($this->tmpDir . '/foo.txt', 'foo');
        $this->assertEquals('foo.txt', $this->fs->basename($this->tmpDir . '/foo.txt'));
    }

    public function testFileTypeAndFileMimeType()
    {
        file_put_contents($this->tmpDir . '/foo.txt', 'foo');
        $this->assertEquals('file', $this->fs->type($this->tmpDir . '/foo.txt'));
        $this->assertEquals('dir', $this->fs->type($this->tmpDir));
        $this->assertEquals('text/plain', $this->fs->mimeType($this->tmpDir . '/foo.txt'));
    }

    public function testIsWritable()
    {
        file_put_contents($this->tmpDir . '/foo.txt', 'foo');
        @chmod($this->tmpDir . '/foo.txt', 0444);
        $this->assertFalse($this->fs->isWritable($this->tmpDir . '/foo.txt'));
        @chmod($this->tmpDir . '/foo.txt', 0777);
        $this->assertTrue($this->fs->isWritable($this->tmpDir . '/foo.txt'));
    }

    public function testIsReadable()
    {
        file_put_contents($this->tmpDir . '/foo.txt', 'foo');

        // chmod is noneffective on Windows
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->assertTrue($this->fs->isReadable($this->tmpDir . '/foo.txt'));
        } else {
            @chmod($this->tmpDir . '/foo.txt', 0000);
            $this->assertFalse($this->fs->isReadable($this->tmpDir . '/foo.txt'));
            @chmod($this->tmpDir . '/foo.txt', 0777);
            $this->assertTrue($this->fs->isReadable($this->tmpDir . '/foo.txt'));
        }
        $this->assertFalse($this->fs->isReadable($this->tmpDir . '/doesnotexist.txt'));
    }

    public function testGlobFindsFiles()
    {
        file_put_contents($this->tmpDir . '/foo.txt', 'foo');
        file_put_contents($this->tmpDir . '/bar.txt', 'bar');
        $glob = $this->fs->glob($this->tmpDir . '/*.txt');
        $this->assertContains($this->tmpDir . '/foo.txt', $glob);
        $this->assertContains($this->tmpDir . '/bar.txt', $glob);
    }

    public function testListFilesFindsFiles()
    {
        file_put_contents($this->tmpDir . '/foo.txt', 'foo');
        file_put_contents($this->tmpDir . '/bar.txt', 'bar');

        $allFiles = [];

        foreach ($this->fs->allFiles($this->tmpDir) as $file) {
            $allFiles[] = $file;
        }
        $this->assertContains($this->tmpDir . '/foo.txt', $allFiles);
        $this->assertContains($this->tmpDir . '/bar.txt', $allFiles);
    }

    public function testListAllFilesFindsAllFilesRecursive()
    {
        file_put_contents($this->tmpDir . '/foo.txt', 'foo');
        file_put_contents($this->tmpDir . '/bar.txt', 'bar');
        mkdir($this->tmpDir . '/tmp2');
        file_put_contents($this->tmpDir . '/tmp2/bar.txt', 'bar');

        $allFiles = [];

        foreach ($this->fs->allFiles($this->tmpDir, '/^.*\.txt/i', false) as $file) {
            $allFiles[] = $file;
        }

        $this->assertContains($this->tmpDir . '/foo.txt', $allFiles);
        $this->assertNotContains($this->tmpDir . '/bar.txt', $allFiles);
        $this->assertContains($this->tmpDir . '/tmp2/bar.txt', $allFiles);
    }

    public function testListDirectoriesGetsAllDirectories()
    {
        mkdir($this->tmpDir . '/foo');
        mkdir($this->tmpDir . '/bar');

        $directories = $this->fs->directories($this->tmpDir);
        $this->assertContains($this->tmpDir . '/foo', $directories);
        $this->assertContains($this->tmpDir . '/bar', $directories);
    }
}
