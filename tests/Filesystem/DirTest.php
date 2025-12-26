<?php

use Mars\Dir;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class DirTest extends Base
{
    protected $dir_cnt = __DIR__ . '/data/dir-test/';
    protected $dir_read = __DIR__ . '/data/dir-test-read/';
    protected $dir_write = __DIR__ . '/data/dir-test-write/';

    public function testDirCreateAndExists()
    {
        $dirPath = $this->dir_write . 'create-test';
        $dir = new Dir($dirPath);

        // Directory should not exist initially
        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
        $this->assertFalse($dir->exists);

        // Create directory
        $dir->create();
        $this->assertTrue($dir->exists);
        $this->assertSame($dirPath, (string)$dir);

        // Clean up
        rmdir($dirPath);
    }

    public function testDirCopy()
    {
        $source = $this->dir_cnt;
        $dest = $this->dir_write . 'copy-test';

        // Ensure destination does not exist
        if (is_dir($dest)) {
            (new Dir($dest))->delete();
        }

        $dir = new Dir($source);
        $copied = $dir->copy($dest);

        $this->assertTrue(is_dir($dest));
        $this->assertInstanceOf(Dir::class, $copied);

        // Clean up
        new Dir($dest)->delete();
    }

    public function testDirMove()
    {
        $source = $this->dir_write . 'move-test';
        $dest = $this->dir_write . 'move-test-dest';

        mkdir($source);

        $dir = new Dir($source);
        $moved = $dir->move($dest);

        $this->assertFalse(is_dir($source));
        $this->assertTrue(is_dir($dest));
        $this->assertInstanceOf(Dir::class, $moved);

        // Clean up
        rmdir($dest);
    }

    public function testDirDelete()
    {
        $dirPath = $this->dir_write . 'delete-test';
        mkdir($dirPath);

        $dir = new Dir($dirPath);
        $result = $dir->delete();

        $this->assertNull($result);
        $this->assertFalse(is_dir($dirPath));
    }

    public function testDirClean()
    {
        $dirPath = $this->dir_write . 'clean-test';
        mkdir($dirPath);
        file_put_contents($dirPath . '/file.txt', 'test');

        $dir = new Dir($dirPath);
        $dir->clean();

        $this->assertTrue(is_dir($dirPath));
        $this->assertFalse(is_file($dirPath . '/file.txt'));

        // Clean up
        rmdir($dirPath);
    }

    public function testDirContains()
    {
        $dir = new Dir('/var/www');
        $this->assertFalse($dir->contains('/var/www', false));
        $this->assertTrue($dir->contains('/var/www/myfilename.jpg', false));
        $this->assertTrue($dir->contains('/var/www/qqq/myfilename.jpg', false));
        $this->assertFalse($dir->contains('/var/temp/myfilename.jpg', false));

        $this->assertFalse($dir->contains('/var/www/myfilename.jpg'));
    }

    public function testGetDirsAndFiles()
    {
        $dir = new Dir($this->dir_read);

        $dirs = $dir->getDirs(false, true);
        $this->assertContains($this->dir_read . 'a', $dirs);
        $this->assertContains($this->dir_read . 'b', $dirs);

        $files = $dir->getFiles(false, true);
        $this->assertContains($this->dir_read . 'file1.txt', $files);
        $this->assertContains($this->dir_read . 'file2.txt', $files);
    }

    public function testGetFilesTree()
    {
        $dir = new Dir($this->dir_read);
        $tree = $dir->getFilesTree(true, true);

        $this->assertIsArray($tree);
        $this->assertNotEmpty($tree);
    }

    public function testGetDirsTree()
    {
        $dir = new Dir($this->dir_read);
        $tree = $dir->getDirsTree(true, true);

        $this->assertIsArray($tree);
        $this->assertNotEmpty($tree);
    }
}
