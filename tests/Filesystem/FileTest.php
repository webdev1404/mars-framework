<?php

use Mars\File;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class FileTest extends Base
{
    protected $dir_write = __DIR__ . '/data/dir-test-write/';

    public function testFileClassBasicProperties()
    {
        $filename = $this->dir_write . 'testfile.txt';
        file_put_contents($filename, 'test');

        $file = new File($filename);

        $this->assertSame($file->filename, $filename);
        $this->assertSame($file->name, basename($filename));
        $this->assertSame($file->extension, 'txt');
        $this->assertSame($file->stem, 'testfile');
        $this->assertSame($file->exists, true);
        $this->assertSame($file->size, 4);
        $this->assertIsString($file->type);

        unlink($filename);
    }

    public function testFileClassReadWriteDelete()
    {
        $filename = $this->dir_write . 'rwfile.txt';
        $file = new File($filename);

        $written = $file->write('hello world');
        $this->assertSame($written, strlen('hello world'));
        $this->assertSame($file->read(), 'hello world');

        $file->delete();
        $this->assertSame($file->exists, false);
    }

    public function testFileClassCopyMove()
    {
        $filename = $this->dir_write . 'copyfile.txt';
        $dest = $this->dir_write . 'copyfile2.txt';
        file_put_contents($filename, 'copytest');

        $file = new File($filename);
        $copied = $file->copy($dest);
        $this->assertInstanceOf(\Mars\File::class, $copied);
        $this->assertSame(file_get_contents($dest), 'copytest');

        $moved = $file->move($this->dir_write . 'movedfile.txt');
        $this->assertInstanceOf(\Mars\File::class, $moved);
        $this->assertSame(is_file($filename), false);
        $this->assertSame(file_get_contents($this->dir_write . 'movedfile.txt'), 'copytest');

        unlink($dest);
        unlink($this->dir_write . 'movedfile.txt');
    }

    public function testFileClassAppendAndAddExtension()
    {
        $filename = $this->dir_write . 'appendfile.txt';
        file_put_contents($filename, 'abc');
        $file = new File($filename);

        $appended = $file->append('_v2');
        $this->assertSame((string)$appended, $file->dir . '/' . $file->stem . '_v2.' . $file->extension);

        $added = $file->addExtension('bak');
        $this->assertSame((string)$added, $filename . '.bak');

        unlink($filename);
    }

    public function testFileClassCheckForInvalidCharsThrows()
    {
        $this->expectException(\Exception::class);
        (new File('../badfile.txt'))->checkForInvalidChars();

        $this->expectException(\Exception::class);
        (new File('php://input'))->checkForInvalidChars();
    }
}
