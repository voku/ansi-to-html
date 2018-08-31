<?php

use voku\AnsiConverter\AnsiToHtmlConverter;

class AnsiToHtmlConverterDefaultThemeWithClassesTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @dataProvider getConvertData
   */
  public function testConvert($expectedOutput, $expectedCss, $input)
  {
    $converter = new AnsiToHtmlConverter(null, false);
    $this->assertEquals($expectedOutput, $converter->convert($input));
    $this->assertEquals($expectedCss, $converter->getTheme()->asCss());
  }

  public function getConvertData()
  {
    $css = <<< 'END_CSS'
.ansi_color_fg_black { color: black }
.ansi_color_bg_black { background-color: black }
.ansi_color_fg_red { color: darkred }
.ansi_color_bg_red { background-color: darkred }
.ansi_color_fg_green { color: green }
.ansi_color_bg_green { background-color: green }
.ansi_color_fg_yellow { color: yellow }
.ansi_color_bg_yellow { background-color: yellow }
.ansi_color_fg_blue { color: blue }
.ansi_color_bg_blue { background-color: blue }
.ansi_color_fg_magenta { color: darkmagenta }
.ansi_color_bg_magenta { background-color: darkmagenta }
.ansi_color_fg_cyan { color: cyan }
.ansi_color_bg_cyan { background-color: cyan }
.ansi_color_fg_white { color: white }
.ansi_color_bg_white { background-color: white }
.ansi_color_fg_brblack { color: black }
.ansi_color_bg_brblack { background-color: black }
.ansi_color_fg_brred { color: red }
.ansi_color_bg_brred { background-color: red }
.ansi_color_fg_brgreen { color: lightgreen }
.ansi_color_bg_brgreen { background-color: lightgreen }
.ansi_color_fg_bryellow { color: lightyellow }
.ansi_color_bg_bryellow { background-color: lightyellow }
.ansi_color_fg_brblue { color: lightblue }
.ansi_color_bg_brblue { background-color: lightblue }
.ansi_color_fg_brmagenta { color: magenta }
.ansi_color_bg_brmagenta { background-color: magenta }
.ansi_color_fg_brcyan { color: lightcyan }
.ansi_color_bg_brcyan { background-color: lightcyan }
.ansi_color_fg_brwhite { color: white }
.ansi_color_bg_brwhite { background-color: white }
.ansi_color_underlined { text-decoration: underlined }
END_CSS;

    return [
      // text is escaped
      ['<span class="ansi_color_bg_black ansi_color_fg_white">foo &lt;br /&gt;</span>', $css, 'foo <br />'],

      // newlines are preserved
      ["<span class=\"ansi_color_bg_black ansi_color_fg_white\">foo\nbar</span>", $css, "foo\nbar"],

      // backspaces
      ['<span class="ansi_color_bg_black ansi_color_fg_white">foo   </span>', $css, "foobar\x08\x08\x08   "],
      [
          '<span class="ansi_color_bg_black ansi_color_fg_white">foo</span><span class="ansi_color_bg_black ansi_color_fg_white">   </span>',
          $css,
          "foob\e[31;41ma\e[0mr\x08\x08\x08   ",
      ],

      // color
      ['<span class="ansi_color_bg_red ansi_color_fg_red">foo</span>', $css, "\e[31;41mfoo\e[0m"],

      // color with [m as a termination (equivalent to [0m])
      ['<span class="ansi_color_bg_red ansi_color_fg_red">foo</span>', $css, "\e[31;41mfoo\e[m"],

      // bright color
      ['<span class="ansi_color_bg_brred ansi_color_fg_brred">foo</span>', $css, "\e[31;41;1mfoo\e[0m"],

      // carriage returns
      ['<span class="ansi_color_bg_black ansi_color_fg_white">foobar</span>', $css, "foo\rbar\rfoobar"],

      // underline
      ['<span class="ansi_color_bg_black ansi_color_fg_white ansi_color_underlined">foo</span>', $css, "\e[4mfoo\e[0m"],

      // non valid unicode codepoints substitution (only available with PHP >= 5.4)
      [
          '<span class="ansi_color_bg_black ansi_color_fg_white">foo ' . "\xEF\xBF\xBD" . '</span>',
          $css,
          "foo \xF4\xFF\xFF\xFF",
      ],

      // Yellow on green.
      ['<span class="ansi_color_bg_green ansi_color_fg_yellow">foo</span>', $css, "\e[33;42mfoo\e[0m"],

      // Yellow on green - reversed.
      ['<span class="ansi_color_bg_yellow ansi_color_fg_green">foo</span>', $css, "\e[33;42;7mfoo\e[0m"],
    ];
  }
}
