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

        $image = $this->app->image;
        $image->getWidth('image.xxx');
    }

    public function testJpg()
    {
        $image = $this->app->image;
        $image_filename = $this->images_dir . 'image.jpg';

        $this->assertFalse($image->isValid($this->images_dir . 'invalid-image.jpg'));
        $this->assertTrue($image->isValid($image_filename));
        $this->assertSame($image->getSize($image_filename), [1280, 853]);
        $this->assertSame($image->getWidth($image_filename), 1280);
        $this->assertSame($image->getHeight($image_filename), 853);

        //resize - both width and height
        $image_filename_resized = $this->images_dir . 'image-resized.jpg';
        $image->resize($image_filename, $image_filename_resized, 400, 200);

        $this->assertTrue(is_file($image_filename_resized));
        $this->assertTrue($image->isValid($image_filename_resized));
        $this->assertSame($image->getSize($image_filename_resized), [400, 200]);
        unlink($image_filename_resized);

        //resize - by width
        $image->resize($image_filename, $image_filename_resized, 400);

        $this->assertTrue(is_file($image_filename_resized));
        $this->assertTrue($image->isValid($image_filename_resized));
        $this->assertSame($image->getSize($image_filename_resized), [400, (int) (400 / $image->getRatio($image_filename))]);
        unlink($image_filename_resized);

        //resize - by height
        $image->resize($image_filename, $image_filename_resized, 0, 400);

        $this->assertTrue(is_file($image_filename_resized));
        $this->assertTrue($image->isValid($image_filename_resized));
        $this->assertSame($image->getSize($image_filename_resized), [(int) (400 * $image->getRatio($image_filename)), 400]);
        unlink($image_filename_resized);


        //crop
        $image_filename_cropped = $this->images_dir . 'image-cropped.jpg';
        $image->crop($image_filename, $image_filename_cropped, 400, 200);

        $this->assertTrue(is_file($image_filename_cropped));
        $this->assertTrue($image->isValid($image_filename_cropped));
        $this->assertSame($image->getSize($image_filename_cropped), [400, 200]);
        unlink($image_filename_cropped);

        $image->crop($image_filename, $image_filename_cropped, 200, 400);
        
        $this->assertTrue(is_file($image_filename_cropped));
        $this->assertTrue($image->isValid($image_filename_cropped));
        $this->assertSame($image->getSize($image_filename_cropped), [200, 400]);
        unlink($image_filename_cropped);


        //cut
        $image_filename_cut = $this->images_dir . 'image-cut.jpg';
        $image->cut($image_filename, $image_filename_cut, 400, 200);

        $this->assertTrue(is_file($image_filename_cut));
        $this->assertTrue($image->isValid($image_filename_cut));
        $this->assertSame($image->getSize($image_filename_cut), [400, 200]);
        unlink($image_filename_cut);

        $image->cut($image_filename, $image_filename_cut, 200, 400);

        $this->assertTrue(is_file($image_filename_cut));
        $this->assertTrue($image->isValid($image_filename_cut));
        $this->assertSame($image->getSize($image_filename_cut), [200, 400]);
        unlink($image_filename_cut);


        //convert
        $image_filename_png = $this->images_dir . 'image-convert.png';
        $image->convert($image_filename, $image_filename_png);

        $this->assertTrue(is_file($image_filename_png));
        $this->assertTrue($image->isValid($image_filename_png));
        unlink($image_filename_png);

        $image_filename_gif = $this->images_dir . 'image-convert.gif';
        $image->convert($image_filename, $image_filename_gif);

        $this->assertTrue(is_file($image_filename_gif));
        $this->assertTrue($image->isValid($image_filename_gif));
        unlink($image_filename_gif);

        $image_filename_webp = $this->images_dir . 'image-convert.webp';
        $image->convert($image_filename, $image_filename_webp);

        $this->assertTrue(is_file($image_filename_webp));
        $this->assertTrue($image->isValid($image_filename_webp));
        unlink($image_filename_webp);

        $image_filename_avif = $this->images_dir . 'image-convert.avif';
        $image->convert($image_filename, $image_filename_avif);

        $this->assertTrue(is_file($image_filename_avif));
        $this->assertTrue($image->isValid($image_filename_avif));
        unlink($image_filename_avif);
    }

    public function testJpeg()
    {
        $image = $this->app->image;
        
        $this->assertFalse($image->isValid($this->images_dir . 'invalid-image.jpeg'));
        $this->assertTrue($image->isValid($this->images_dir . 'image.jpeg'));
    }
}
