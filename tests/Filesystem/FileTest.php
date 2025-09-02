<?php

use Mars\File;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class FileTest extends Base
{
    protected $dir_write = __DIR__ . '/data/dir-test-write/';

    public function testCheckForInvalidChars()
    {
        $this->expectException(\Exception::class);
        $this->app->file->checkForInvalidChars('../somefile.txt');

        $this->expectException(\Exception::class);
        $this->app->file->checkForInvalidChars('./../somefile.txt');

        $this->app->file->checkForInvalidChars('somefile.txt');
    }

    public function testCheckFilename()
    {
        $this->expectException(\Exception::class);
        $this->app->file->checkFilename('../somefile.txt');

        $this->expectException(\Exception::class);
        $this->app->file->checkFilename('./../somefile.txt');

        $this->app->config->open_basedir = true;
        $file = new File($this->app);

        $this->expectException(\Exception::class);
        $file->checkFilename('/etc/php/somefile.txt');

        $this->app->config->open_basedir = '/etc/php';
        $file = new File($this->app);
        $file->checkFilename('/etc/php/somefile.txt');

        $this->app->config->open_basedir = '';
        $file = new File($this->app);
        $file->checkFilename('/etc/php/somefile.txt');
    }

    public function testGet()
    {
        $filename = 'test.txt';
        $fileInfo = $this->app->file->get($filename);
        $this->assertInstanceOf(\SplFileInfo::class, $fileInfo);
    }

    public function testSlash()
    {
        $this->assertSame($this->app->file->slash('/var/www/html'), '/var/www/html/');
        $this->assertSame($this->app->file->slash('/var/www/html/'), '/var/www/html/');
        $this->assertSame($this->app->file->slash(''), '');
    }

    public function testGetDir()
    {
        $this->assertSame($this->app->file->getDir('/var/www/html/somefile.txt'), '/var/www/html');
        $this->assertSame($this->app->file->getDir('.'), '');
    }

    public function testGetRel()
    {
        $this->assertSame($this->app->file->getRel($this->app->base_path . '/somefile.txt'), 'somefile.txt');
        $this->assertSame($this->app->file->getRel($this->app->base_path . '/dir1/dir2/somefile.txt'), 'dir1/dir2/somefile.txt');
        $this->assertSame($this->app->file->getRel('/etc/php/somefile.txt'), '/etc/php/somefile.txt');
        $this->assertSame($this->app->file->getRel('somefile.txt'), 'somefile.txt');

        $this->assertSame($this->app->file->getRel('/dir1/dir2/somefile.txt', '/dir1/dir2'), 'somefile.txt');
    }

    public function testGetStem()
    {
        $file = $this->app->file;

        $this->assertSame($this->app->file->getStem('/etc/php/somefile.txt'), 'somefile');
        $this->assertSame($this->app->file->getStem('/etc/php/../somefile.txt'), 'somefile');
        $this->assertSame($this->app->file->getStem('somefile.txt'), 'somefile');
        $this->assertSame($this->app->file->getStem('somefile'), 'somefile');
    }

    public function testGetName()
    {
        $file = $this->app->file;

        $this->assertSame($this->app->file->getName('/etc/php/somefile.txt'), 'somefile.txt');
        $this->assertSame($this->app->file->getName('/etc/php/../somefile.txt'), 'somefile.txt');
        $this->assertSame($this->app->file->getName('somefile.txt'), 'somefile.txt');
        $this->assertSame($this->app->file->getName('somefile'), 'somefile');
    }

    public function testAppendToFilename()
    {
        $file = $this->app->file;

        $this->assertSame($this->app->file->appendToFilename('/etc/php/somefile.txt', '_1234'), '/etc/php/somefile_1234.txt');
        $this->assertSame($this->app->file->appendToFilename('/etc/php/somefile', '_1234'), '/etc/php/somefile_1234');
        $this->assertSame($this->app->file->appendToFilename('somefile.txt', '_1234'), 'somefile_1234.txt');
    }

    public function testGetExtension()
    {
        $file = $this->app->file;

        $this->assertSame($this->app->file->getExtension('/etc/php/somefile.txt'), 'txt');
        $this->assertSame($this->app->file->getExtension('somefile.txt'), 'txt');
        $this->assertSame($this->app->file->getExtension('somefile'), '');
        $this->assertSame($this->app->file->getExtension('somefile.jpg.txt'), 'txt');

        $this->assertSame($this->app->file->getExtension('somefile.txt', true), '.txt');
        $this->assertSame($this->app->file->getExtension('somefile', true), '');
    }

    public function testAddExtension()
    {
        $file = $this->app->file;

        $this->assertSame($this->app->file->addExtension('/etc/php/somefile', 'txt'), '/etc/php/somefile.txt');
        $this->assertSame($this->app->file->addExtension('somefile', 'txt'), 'somefile.txt');
        $this->assertSame($this->app->file->addExtension('', 'txt'), '.txt');
        $this->assertSame($this->app->file->addExtension('somefile.jpg', 'txt'), 'somefile.jpg.txt');
    }

    public function testBuildPath()
    {
        $file = $this->app->file;

        $this->assertSame($this->app->file->buildPath([]), '');
        $this->assertSame($this->app->file->buildPath(['var', 'www', 'html']), '/var/www/html');
        $this->assertSame($this->app->file->buildPath(['var', 'www', '', 'html', 'somefile.txt']), '/var/www/html/somefile.txt');
    }

    public function testGetSubdir()
    {
        $file = $this->app->file;

        $this->assertSame($this->app->file->getSubdir('somefolder'), 'some');
        $this->assertSame($this->app->file->getSubdir('somefolder', false, 6), 'somefo');
    }

    public function testIsImage()
    {
        $file = $this->app->file;

        $this->assertSame($this->app->file->isImage('somefile'), false);
        $this->assertSame($this->app->file->isImage('somefile.jpg'), true);
        $this->assertSame($this->app->file->isImage('/var/www/html/somefile.jpg'), true);
    }

    public function testGetRandomFilename()
    {
        $filename = $this->app->file->getRandomFilename('txt');
        $this->assertStringEndsWith('.txt', $filename);
    }

    public function testGetTmpFilename()
    {
        $tmpFilename = $this->app->file->getTmpFilename('test.txt');
        $this->assertStringContainsString('test.txt', $tmpFilename);
    }

    public function testRead()
    {
        $file = $this->app->file;
        $filename = $this->dir_write . 'myfile.txt';

        $this->expectException(\Exception::class);
        $this->app->file->read('/etc/php/somefile.txt');

        file_put_contents($filename, 'abc');
        $this->assertSame($this->app->file->read($filename), 'abc');
        unlink($filename);
    }

    public function testWrite()
    {
        $file = $this->app->file;
        $filename = $this->dir_write . 'myfile.txt';

        $this->expectException(\Exception::class);
        $this->app->file->write('/etc/php/somefile.txt', 'abc');

        $this->app->file->write($filename, 'abc');

        $this->assertSame(file_get_contents($filename), 'abc');

        unlink($filename);
    }

    public function testDelete()
    {
        $file = $this->app->file;
        $filename = $this->dir_write . 'myfile.txt';

        file_put_contents($filename, 'abc');
        $this->app->file->delete($filename);

        $this->assertSame(is_file($filename), false);
    }

    public function testCopy()
    {
        $file = $this->app->file;
        $filename = $this->dir_write . 'myfile.txt';
        $filename_dest = $this->dir_write . 'myfile2.txt';

        file_put_contents($filename, 'abc');
        $this->app->file->copy($filename, $filename_dest);

        $this->assertSame(is_file($filename), true);
        $this->assertSame(is_file($filename_dest), true);
        $this->assertSame(file_get_contents($filename_dest), 'abc');

        unlink($filename);
        unlink($filename_dest);
    }

    public function testMove()
    {
        $file = $this->app->file;
        $filename = $this->dir_write . 'myfile.txt';
        $filename_dest = $this->dir_write . 'myfile2.txt';

        file_put_contents($filename, 'abc');
        $this->app->file->move($filename, $filename_dest);

        $this->assertSame(is_file($filename), false);
        $this->assertSame(is_file($filename_dest), true);
        $this->assertSame(file_get_contents($filename_dest), 'abc');

        unlink($filename_dest);
    }
}
