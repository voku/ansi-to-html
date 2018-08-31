<?php

use voku\AnsiConverter\AnsiToHtmlConverter;
use voku\AnsiConverter\Theme\SolarizedTheme;

class AnsiToHtmlConverterWithAlternativeThemeWithInlineStylesTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @dataProvider getConvertData
   */
  public function testConvertWith($expected, $input)
  {
    $converter = new AnsiToHtmlConverter(new SolarizedTheme());
    $this->assertEquals($expected, $converter->convert($input));
  }

  public function getConvertData()
  {
    return [
      // text is escaped
      ['<span style="background-color: #073642; color: #eee8d5;">foo &lt;br /&gt;</span>', 'foo <br />'],

      // newlines are preserved
      ["<span style=\"background-color: #073642; color: #eee8d5;\">foo\nbar</span>", "foo\nbar"],

      // backspaces
      ['<span style="background-color: #073642; color: #eee8d5;">foo   </span>', "foobar\x08\x08\x08   "],
      [
          '<span style="background-color: #073642; color: #eee8d5;">foo</span><span style="background-color: #073642; color: #eee8d5;">   </span>',
          "foob\e[31;41ma\e[0mr\x08\x08\x08   ",
      ],

      // color
      ['<span style="background-color: #dc322f; color: #dc322f;">foo</span>', "\e[31;41mfoo\e[0m"],

      // color with [m as a termination (equivalent to [0m])
      ['<span style="background-color: #dc322f; color: #dc322f;">foo</span>', "\e[31;41mfoo\e[m"],

      // bright color
      ['<span style="background-color: #cb4b16; color: #cb4b16;">foo</span>', "\e[31;41;1mfoo\e[0m"],

      // carriage returns
      ['<span style="background-color: #073642; color: #eee8d5;">foobar</span>', "foo\rbar\rfoobar"],

      // underline
      [
          '<span style="background-color: #073642; color: #eee8d5; text-decoration: underline;">foo</span>',
          "\e[4mfoo\e[0m",
      ],

      // non valid unicode codepoints substitution (only available with PHP >= 5.4)
      [
          '<span style="background-color: #073642; color: #eee8d5;">foo ' . "\xEF\xBF\xBD" . '</span>',
          "foo \xF4\xFF\xFF\xFF",
      ],

      // Yellow on green.
      ['<span style="background-color: #859900; color: #b58900;">foo</span>', "\e[33;42mfoo\e[0m"],

      // Yellow on green - reversed.
      ['<span style="background-color: #b58900; color: #859900;">foo</span>', "\e[33;42;7mfoo\e[0m"],
    ];
  }
}
