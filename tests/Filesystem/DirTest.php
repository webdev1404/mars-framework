<?php

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class DirTest extends Base
{
    protected $dir_cnt = __DIR__ . '/data/dir-test/';
    protected $dir_read = __DIR__ . '/data/dir-test-read/';
    protected $dir_write = __DIR__ . '/data/dir-test-write/';

    public function testContains()
    {
        $this->assertSame($this->app->dir->contains('/var/www', '/var/www/myfilename.jpg'), true);
        $this->assertSame($this->app->dir->contains('/var/www/', '/var/www/myfilename.jpg'), true);
        $this->assertSame($this->app->dir->contains('/var/www/', '/var/www/qqq/myfilename.jpg'), true);
        $this->assertSame($this->app->dir->contains('/var/www/', '/var/temp/myfilename.jpg'), false);
        $this->assertSame($this->app->dir->contains('/var/temp/', '/var/www/myfilename.jpg'), false);
    }

    public function testGetDir()
    {
        $this->assertEqualsCanonicalizing($this->app->dir->getDirs($this->dir_read), [$this->dir_read . 'a', $this->dir_read . 'b']);
        $this->assertEqualsCanonicalizing($this->app->dir->getDirs($this->dir_read, true), [$this->dir_read . 'a', $this->dir_read . 'a/aa', $this->dir_read . 'b']);
        $this->assertEqualsCanonicalizing($this->app->dir->getDirs($this->dir_read, true, false), ['a', 'a/aa', 'b']);
        $this->assertEqualsCanonicalizing($this->app->dir->getDirs($this->dir_read, true, true, ['b']), [$this->dir_read . 'a', $this->dir_read . 'a/aa']);
    }

    public function testGetFiles()
    {
        $this->assertEqualsCanonicalizing($this->app->dir->getFiles($this->dir_read), [$this->dir_read . 'file1.txt', $this->dir_read . 'file2.txt', $this->dir_read . 'file3.emp']);
        $this->assertEqualsCanonicalizing($this->app->dir->getFiles($this->dir_read, true), [$this->dir_read . 'file1.txt', $this->dir_read . 'file2.txt', $this->dir_read . 'file3.emp', $this->dir_read . 'a/file3.txt', $this->dir_read . 'a/aa/file4.txt', $this->dir_read . 'b/file4.txt']);            
        $this->assertEqualsCanonicalizing($this->app->dir->getFiles($this->dir_read, true, false), ['file1.txt', 'file2.txt', 'file3.emp', 'a/file3.txt', 'a/aa/file4.txt' , 'b/file4.txt']);
        $this->assertEqualsCanonicalizing($this->app->dir->getFiles($this->dir_read, false, false), ['file1.txt', 'file2.txt', 'file3.emp']);
    }

    public function testCreate()
    {
        $this->app->dir->create($this->dir_write . 'test');
        $this->assertSame(is_dir($this->dir_write . 'test'), true);
        rmdir($this->dir_write . 'test');
    }

    public function testCopy()
    {
        $dir = $this->app->dir;

        $source_dir = $this->dir_cnt;
        $dest_dir = $this->dir_write . 'test/';

        $this->app->dir->copy($source_dir, $dest_dir);
        $this->assertSame(is_dir($dest_dir . 'a'), true);
        $this->assertSame(is_file($dest_dir . 'file1.txt'), true);
        $this->assertSame(is_file($dest_dir . 'a/file2.txt'), true);

        unlink($dest_dir . 'file1.txt');
        unlink($dest_dir . 'a/file2.txt');

        rmdir($dest_dir . 'a');
        rmdir($dest_dir);
    }

    public function testMove()
    {
        $dir = $this->app->dir;

        $source_dir = $this->dir_write . 'test/';
        $dest_dir = $this->dir_write . 'test1/';

        mkdir($source_dir);

        $this->app->dir->move($source_dir, $dest_dir);

        $this->assertSame(is_dir($source_dir), false);
        $this->assertSame(is_dir($dest_dir), true);

        rmdir($dest_dir);
    }

    public function testDelete()
    {
        $dir = $this->app->dir;

        $source_dir = $this->dir_cnt;
        $dest_dir = $this->dir_write . 'test/';

        $this->app->dir->copy($source_dir, $dest_dir);
        $this->app->dir->delete($dest_dir);

        $this->assertSame(is_dir($dest_dir), false);
    }

    public function testClean()
    {
        $dir = $this->app->dir;

        $source_dir = $this->dir_cnt;
        $dest_dir = $this->dir_write . 'test/';

        $this->app->dir->copy($source_dir, $dest_dir);
        $this->app->dir->clean($dest_dir);

        $this->assertSame(is_dir($dest_dir), true);
        $this->assertSame(is_dir($dest_dir . 'a'), false);
        $this->assertSame(is_file($dest_dir . 'file1.txt'), false);

        rmdir($dest_dir);
    }
}
