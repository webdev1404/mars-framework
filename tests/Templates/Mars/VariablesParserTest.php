<?php

use Mars\Templates\Mars\VariablesParser;

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class VariablesParserTest extends Base
{
    public function testParse()
    {
        $parser = new VariablesParser($this->app);
        $this->assertSame($parser->parse('{{ my_str123 }}'), '<?= $this->app->escape->html($strings[\'my_str123\'] ?? \'my_str123\') ?>');
        $this->assertSame($parser->parse('{{ $myvar }}'), '<?= $this->app->escape->html($myvar) ?>');
        $this->assertSame($parser->parse('{{ $myobj.prop }}'), '<?= $this->app->escape->html($myobj->prop) ?>');
        $this->assertSame($parser->parse('{{ $myobj->prop }}'), '<?= $this->app->escape->html($myobj->prop) ?>');
        $this->assertSame($parser->parse('{{ $myarr@prop }}'), '<?= $this->app->escape->html($myarr[\'prop\']) ?>');
        $this->assertSame($parser->parse('{{ $myarr[\'prop\'] }}'), '<?= $this->app->escape->html($myarr[\'prop\']) ?>');

        $this->assertSame($parser->parse('{{ my_str123|raw }}'), '<?= $strings[\'my_str123\'] ?? \'my_str123\' ?>');
        $this->assertSame($parser->parse('{{ $myvar | raw }}'), '<?= $myvar ?>');
        $this->assertSame($parser->parse('{{ $myvar | lower| trim }}'), '<?= $this->app->escape->html(strtolower(trim($myvar))) ?>');
        $this->assertSame($parser->parse('{{ $myvar | raw | lower | trim }}'), '<?= strtolower(trim($myvar)) ?>');

        $this->assertSame($parser->parse('{{ trim($myvar) }}'), '<?= $this->app->escape->html(trim($myvar)) ?>');
        $this->assertSame($parser->parse('{{ trim($myvar) | raw }}'), '<?= trim($myvar) ?>');
        $this->assertSame($parser->parse('{{ myfunc($myvar) | trim }}'), '<?= $this->app->escape->html(trim(myfunc($myvar))) ?>');
        $this->assertSame($parser->parse('{{ myfunc($myvar) | raw | trim }}'), '<?= trim(myfunc($myvar)) ?>');
    }
}
