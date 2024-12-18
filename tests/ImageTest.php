<?php

use Mars\Image;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class ImageTest extends Base
{
    protected $images_dir = __DIR__ . '/data/images/';

    public function testUnsupported()
    {
        $this->expectException(\Exception::class);

        $this->app->image->getWidth('image.xxx');
    }

    public function testJpg()
    {
        $image_filename = $this->images_dir . 'image.jpg';

        $this->assertFalse($this->app->image->isValid($this->images_dir . 'invalid-image.jpg'));
        $this->assertTrue($this->app->image->isValid($image_filename));
        $this->assertSame($this->app->image->getSize($image_filename), [1280, 853]);
        $this->assertSame($this->app->image->getWidth($image_filename), 1280);
        $this->assertSame($this->app->image->getHeight($image_filename), 853);

        //resize - both width and height
        $image_filename_resized = $this->images_dir . 'image-resized.jpg';
        $this->app->image->resize($image_filename, $image_filename_resized, 400, 200);

        $this->assertTrue(is_file($image_filename_resized));
        $this->assertTrue($this->app->image->isValid($image_filename_resized));
        $this->assertSame($this->app->image->getSize($image_filename_resized), [400, 200]);
        unlink($image_filename_resized);

        //resize - by width
        $this->app->image->resize($image_filename, $image_filename_resized, 400);

        $this->assertTrue(is_file($image_filename_resized));
        $this->assertTrue($this->app->image->isValid($image_filename_resized));
        $this->assertSame($this->app->image->getSize($image_filename_resized), [400, (int) (400 / $this->app->image->getRatio($image_filename))]);
        unlink($image_filename_resized);

        //resize - by height
        $this->app->image->resize($image_filename, $image_filename_resized, 0, 400);

        $this->assertTrue(is_file($image_filename_resized));
        $this->assertTrue($this->app->image->isValid($image_filename_resized));
        $this->assertSame($this->app->image->getSize($image_filename_resized), [(int) (400 * $this->app->image->getRatio($image_filename)), 400]);
        unlink($image_filename_resized);


        //crop
        $image_filename_cropped = $this->images_dir . 'image-cropped.jpg';
        $this->app->image->crop($image_filename, $image_filename_cropped, 400, 200);

        $this->assertTrue(is_file($image_filename_cropped));
        $this->assertTrue($this->app->image->isValid($image_filename_cropped));
        $this->assertSame($this->app->image->getSize($image_filename_cropped), [400, 200]);
        unlink($image_filename_cropped);

        $this->app->image->crop($image_filename, $image_filename_cropped, 200, 400);
        
        $this->assertTrue(is_file($image_filename_cropped));
        $this->assertTrue($this->app->image->isValid($image_filename_cropped));
        $this->assertSame($this->app->image->getSize($image_filename_cropped), [200, 400]);
        unlink($image_filename_cropped);


        //cut
        $image_filename_cut = $this->images_dir . 'image-cut.jpg';
        $this->app->image->cut($image_filename, $image_filename_cut, 400, 200);

        $this->assertTrue(is_file($image_filename_cut));
        $this->assertTrue($this->app->image->isValid($image_filename_cut));
        $this->assertSame($this->app->image->getSize($image_filename_cut), [400, 200]);
        unlink($image_filename_cut);

        $this->app->image->cut($image_filename, $image_filename_cut, 200, 400);

        $this->assertTrue(is_file($image_filename_cut));
        $this->assertTrue($this->app->image->isValid($image_filename_cut));
        $this->assertSame($this->app->image->getSize($image_filename_cut), [200, 400]);
        unlink($image_filename_cut);


        //convert
        $image_filename_png = $this->images_dir . 'image-convert.png';
        $this->app->image->convert($image_filename, $image_filename_png);

        $this->assertTrue(is_file($image_filename_png));
        $this->assertTrue($this->app->image->isValid($image_filename_png));
        unlink($image_filename_png);

        $image_filename_gif = $this->images_dir . 'image-convert.gif';
        $this->app->image->convert($image_filename, $image_filename_gif);

        $this->assertTrue(is_file($image_filename_gif));
        $this->assertTrue($this->app->image->isValid($image_filename_gif));
        unlink($image_filename_gif);

        $image_filename_webp = $this->images_dir . 'image-convert.webp';
        $this->app->image->convert($image_filename, $image_filename_webp);

        $this->assertTrue(is_file($image_filename_webp));
        $this->assertTrue($this->app->image->isValid($image_filename_webp));
        unlink($image_filename_webp);

        $image_filename_avif = $this->images_dir . 'image-convert.avif';
        $this->app->image->convert($image_filename, $image_filename_avif);

        $this->assertTrue(is_file($image_filename_avif));
        $this->assertTrue($this->app->image->isValid($image_filename_avif));
        unlink($image_filename_avif);
    }

    public function testJpeg()
    {
        $this->assertFalse($this->app->image->isValid($this->images_dir . 'invalid-image.jpeg'));
        $this->assertTrue($this->app->image->isValid($this->images_dir . 'image.jpeg'));
    }
}
