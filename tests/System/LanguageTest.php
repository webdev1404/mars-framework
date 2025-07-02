<?php

use Mars\System\Language;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class LanguageTest extends Base
{
    public function testLoadFilename()
    {
        $filename = __DIR__ . '/data/strings.php';

        $language = new Language($this->app);
        $language->strings = [];
        $language->loadFilename($filename);

        $this->assertSame($language->strings, ['str1' => 'String 1', 'str2' => 'String 2']);
    }
}
