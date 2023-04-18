<?php

declare(strict_types=1);

namespace Ob\HighchartsBundle\Tests\Highcharts;

use Ob\HighchartsBundle\Highcharts\Highchart;
use PHPUnit\Framework\TestCase;

/**
 * This class hold Unit Tests for the colors option.
 */
class ColorsTest extends TestCase
{
    /**
     * Series output.
     */
    public function testColors(): void
    {
        $chart = new Highchart();
        $colors = ['#FF0000', '#00FF00', '#0000FF'];
        $chart->colors($colors);
        $this->assertMatchesRegularExpression('/colors: \[\["#FF0000","#00FF00","#0000FF"\]\]/', $chart->render());
    }
}
