<?php

declare(strict_types=1);

namespace Ob\HighchartsBundle\Tests\Highstock;

use Ob\HighchartsBundle\Highcharts\ChartOption;
use Ob\HighchartsBundle\Highcharts\Highstock;
use Ob\HighchartsBundle\Tests\AbstractChartTestCase;

class CreditsTest extends AbstractChartTestCase
{
    protected ?Highstock $chart = null;
    protected ?ChartOption $credits = null;

    protected function setUp(): void
    {
        $this->chart = new Highstock();
        $this->credits = $this->chart->credits;
    }

    public function testEnabled(): void
    {
        $this->assertNotNull($this->chart);
        $this->assertNotNull($this->credits);
        $this->credits->enabled(true);
        $this->assertTrue($this->credits->enabled);
        $regex = '/"enabled":true/';
        $this->assertChartMatchesRegularExpression($this->chart, $regex);

        $this->credits->enabled(false);
        $this->assertFalse($this->credits->enabled);
        $regex = '/"enabled":false/';
        $this->assertChartMatchesRegularExpression($this->chart, $regex);
    }

    public function testHref(): void
    {
        $this->assertNotNull($this->chart);
        $this->assertNotNull($this->credits);
        $link = 'http://www.highcharts.com';
        $this->credits->href($link);
        $this->assertSame($link, $this->credits->href);
    }

    public function testPosition(): void
    {
        $position = [
            'align' => 'right',
            'x' => -10,
            'verticalAlign' => 'bottom',
            'y' => -5,
        ];
        $this->assertNotNull($this->chart);
        $this->assertNotNull($this->credits);
        $this->credits->position($position);
        $this->assertSame($this->credits->position, $position);
        $regex = '/"position":{"align":"right","x":-10,"verticalAlign":"bottom","y":-5}/';
        $this->assertChartMatchesRegularExpression($this->chart, $regex);
    }

    public function testStyle(): void
    {
        $style = [
            'cursor' => 'pointer',
            'color' => '#909090',
            'fontSize' => '10px',
        ];
        $this->assertNotNull($this->chart);
        $this->assertNotNull($this->credits);
        $this->credits->style($style);
        $this->assertSame($style, $this->credits->style);
        $regex = '/"style":{"cursor":"pointer","color":"#909090","fontSize":"10px"}/';
        $this->assertChartMatchesRegularExpression($this->chart, $regex);
    }

    public function testText(): void
    {
        $this->assertNotNull($this->chart);
        $this->assertNotNull($this->credits);
        $text = 'Highcharts.com';
        $this->credits->text($text);
        $this->assertSame($text, $this->credits->text);
        $regex = '/"text":"Highcharts.com"/';
        $this->assertChartMatchesRegularExpression($this->chart, $regex);
    }
}
