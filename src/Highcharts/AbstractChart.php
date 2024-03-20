<?php
/*
 * This file is part of the HighchartsBundle package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace HighchartsBundle\Highcharts;

use Laminas\Json\Expr;
use Laminas\Json\Json;

/**
 * Abstract chart.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class AbstractChart implements ChartInterface
{
    // the script end line
    private const END_LINE = ",\n";

    // the half space prefix
    private const HALF_SPACE = '    ';

    // the script new line
    private const NEW_LINE = "\n";

    // the space prefix
    private const SPACE = '        ';

    // The Zend json encode options.
    private const ZEND_ENCODE_OPTIONS = ['enableJsonExprFinder' => true];

    public ChartOption $accessibility;
    public ChartOption $chart;
    /** @var string[] */
    public array $colors;
    public ChartOption $credits;
    public ChartOption $exporting;
    public ChartOption $global;
    public ChartOption $lang;
    public ChartOption $legend;
    public ChartOption $plotOptions;
    public ChartOption $scrollbar;
    public ChartOption $series;
    public ChartOption $subtitle;
    public ChartOption $title;
    public ChartOption $tooltip;
    public ChartOption $xAxis;
    public ChartOption $yAxis;

    public function __construct()
    {
        //  options
        $options = [
            'chart', 'credits', 'exporting', 'global', 'lang',
            'legend', 'plotOptions', 'scrollbar', 'series',
            'subtitle', 'title', 'tooltip', 'xAxis', 'yAxis',
            'accessibility',
        ];
        foreach ($options as $option) {
            $this->initChartOption($option);
        }

        // arrays
        $options = ['colors'];
        foreach ($options as $option) {
            $this->initArrayOption($option);
        }
    }

    public function __call(string $name, mixed $value): static
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * Create an expression.
     *
     * @param string $expression the expression to represent
     *
     * @psalm-api
     */
    public static function createExpression(string $expression): Expr
    {
        // remove consecutive spaces
        $expression = (string) \preg_replace('/\s+/', ' ', \trim($expression));

        return new Expr($expression);
    }

    /**
     * @psalm-param ChartInterface::ENGINE_* $engine
     */
    public function render(string $engine = self::ENGINE_JQUERY): string
    {
        $chartJS = '';
        $this->renderChartStart($chartJS, $engine);
        $this->renderChartCommon($chartJS);
        $this->renderChartOptions($chartJS);
        $this->renderChartEnd($chartJS, $engine);

        return \trim($chartJS);
    }

    /**
     * Gets the chart class (type) to create.
     */
    abstract protected function getChartClass(): string;

    /**
     * Gets the chart render to (target) property.
     */
    protected function getRenderTo(): string
    {
        return (string) ($this->chart->renderTo ?? 'chart');
    }

    protected function initArrayOption(string $name): void
    {
        $this->{$name} = [];
    }

    /**
     * @psalm-param non-empty-string $name
     */
    protected function initChartOption(string $name): void
    {
        $this->{$name} = new ChartOption($name);
    }

    protected function jsonEncode(ChartOption|array $data, string $name = ''): string
    {
        if ($data instanceof ChartOption) {
            $name = $data->getName();
            $data = $data->getData();
        }
        if ('' === $name || [] === $data) {
            return '';
        }

        // Zend\Json is used in place of json_encode to preserve JS anonymous functions
        $encoded = $this->isExpression($data)
            ? Json::encode(valueToEncode: $data, options: self::ZEND_ENCODE_OPTIONS)
            : \json_encode($data);

        return self::SPACE . $name . ': ' . $encoded . self::END_LINE;
    }

    protected function renderAccessibility(): string
    {
        return $this->jsonEncode($this->accessibility);
    }

    protected function renderChart(): string
    {
        return $this->jsonEncode($this->chart);
    }

    protected function renderChartClass(): string
    {
        $renderTo = $this->getRenderTo();
        $class = $this->getChartClass();

        return self::NEW_LINE . self::HALF_SPACE . "const $renderTo = new Highcharts.$class({" . self::NEW_LINE;
    }

    protected function renderChartCommon(string &$chartJS): void
    {
        $chartJS .= $this->renderChart();
        $chartJS .= $this->renderColors();
        $chartJS .= $this->renderCredits();
        $chartJS .= $this->renderExporting();
        $chartJS .= $this->renderLegend();
        $chartJS .= $this->renderScrollbar();
        $chartJS .= $this->renderSubtitle();
        $chartJS .= $this->renderTitle();
        $chartJS .= $this->renderXAxis();
        $chartJS .= $this->renderYAxis();
        $chartJS .= $this->renderPlotOptions();
        $chartJS .= $this->renderSeries();
        $chartJS .= $this->renderTooltip();
        $chartJS .= $this->renderAccessibility();
    }

    protected function renderChartEnd(string &$chartJS, string $engine): void
    {
        $chartJS = \rtrim($chartJS, self::END_LINE) . self::NEW_LINE . self::HALF_SPACE . '});' . self::NEW_LINE;
        if ('' !== $engine) {
            $chartJS .= '});' . self::NEW_LINE;
        }
    }

    protected function renderChartOptions(string &$chartJS): void
    {
    }

    protected function renderChartStart(string &$chartJS, string $engine): void
    {
        $chartJS .= $this->renderEngine($engine);
        $chartJS .= $this->renderOptions();
        $chartJS .= $this->renderChartClass();
    }

    protected function renderColors(): string
    {
        return $this->jsonEncode($this->colors, 'colors');
    }

    protected function renderCredits(): string
    {
        return $this->jsonEncode($this->credits);
    }

    protected function renderEngine(string $engine): string
    {
        return match ($engine) {
            self::ENGINE_MOOTOOLS => 'window.addEvent(\'domready\', function () {',
            self::ENGINE_JQUERY => '$(function () {',
            default => '',
        };
    }

    protected function renderExporting(): string
    {
        return $this->jsonEncode($this->exporting);
    }

    protected function renderGlobal(): string
    {
        return $this->jsonEncode($this->global);
    }

    protected function renderLang(): string
    {
        return $this->jsonEncode($this->lang);
    }

    protected function renderLegend(): string
    {
        return $this->jsonEncode($this->legend);
    }

    protected function renderOptions(): string
    {
        if (!$this->global->hasData() && !$this->lang->hasData()) {
            return '';
        }

        $result = self::NEW_LINE . self::HALF_SPACE . 'Highcharts.setOptions({' . self::NEW_LINE;
        $result .= $this->renderGlobal();
        $result .= $this->renderLang();

        return $result . self::HALF_SPACE . '});';
    }

    protected function renderPlotOptions(): string
    {
        return $this->jsonEncode($this->plotOptions);
    }

    protected function renderScrollbar(): string
    {
        return $this->jsonEncode($this->scrollbar);
    }

    protected function renderSeries(): string
    {
        return $this->jsonEncode($this->series);
    }

    protected function renderSubtitle(): string
    {
        return $this->jsonEncode($this->subtitle);
    }

    protected function renderTitle(): string
    {
        return $this->jsonEncode($this->title);
    }

    protected function renderTooltip(): string
    {
        return $this->jsonEncode($this->tooltip);
    }

    protected function renderXAxis(): string
    {
        return $this->jsonEncode($this->xAxis);
    }

    protected function renderYAxis(): string
    {
        return $this->jsonEncode($this->yAxis);
    }

    private function isExpression(array $values): bool
    {
        /** @psalm-var mixed $value */
        foreach ($values as $value) {
            if ($value instanceof Expr) {
                return true;
            } elseif (\is_array($value) && $this->isExpression($value)) {
                return true;
            }
        }

        return false;
    }
}
