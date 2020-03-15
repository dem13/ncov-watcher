<?php


namespace App\Ncov\Chart;


use Imagine\Draw\DrawerInterface;
use Imagine\Gd\Font;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

/**
 * TODO: Line thickness selection
 * TODO: Font side selection
 *
 * Class Chart
 * @package App\Ncov\Chart
 */
class Chart
{
    /**
     * @var array
     */
    private $colors = [
        'background' => "#111115",
        'line' => '#d27230',
        'point' => '#dbb3a4',
        'mark' => '#161620',
        'text' => '#dbb3a4',
    ];

    /**
     * Chart size
     *
     * @var array
     */
    private $size = [4006, 1606];

    /**
     * Chart margin
     *
     * @var array
     */
    private $margin = [50, 70];

    /**
     * Image size
     *
     * @var array
     */
    private $imageSize = [4096, 1716];

    /**
     * Records for chart to draw
     *
     * @var ChartRecord[]
     */
    private $records = [];

    /**
     * A period of time that chart will draw
     * From that first record to that last one
     *
     * @var int
     */
    private $duration;

    /**
     * TODO: Put the highest number here for better performance
     *
     * @var int
     */
    private $highest;

    /**
     * Path to font file
     *
     * @var string
     */
    private $font = '../storage/app/font/Roboto-Black.ttf';

    /**
     * Chart constructor.
     */
    public function __construct()
    {
        $palette = new RGB();

        foreach ($this->colors as $key => $color) {
            $this->colors[$key] = $palette->color($color);
        }
    }

    /**
     * @param array $records
     */
    public function setRecords(array $records): void
    {
        $this->records = $records;
    }

    /**
     * Render chart image
     *
     * @return string
     */
    public function render(): string
    {
        $image = (new Imagine())->create(new Box($this->imageSize[0], $this->imageSize[1]), $this->colors['background']);

        $drawer = $image->draw();

        $this->computeDuration();

        $this->drawTimeMarks($drawer);

        $this->drawValueMarks($drawer);

        $this->drawChart($drawer);

        return $image->get('png');
    }

    /**
     * Get chart duration in minutes
     *
     * @return void
     */
    public function computeDuration(): void
    {
        $this->duration = $this->diff($this->records[0], $this->getLastRecord());
    }

    /**
     * @param ChartRecord $record
     * @param ChartRecord $record2
     * @return int
     */
    public function diff(ChartRecord $record, ChartRecord $record2): int
    {
        return $record->getDate()->diffInMinutes($record2->getDate());
    }

    /**
     * Get last record
     *
     * @return ChartRecord
     */
    public function getLastRecord(): ChartRecord
    {
        return $this->records[count($this->records) - 1];
    }

    /**
     * TODO: Period selection
     *
     * @param DrawerInterface $drawer
     */
    public function drawTimeMarks(DrawerInterface $drawer): void
    {
        [$width, $height] = $this->size;

        $markStep = 1440 * 2;

        $marksDuration = ($this->duration / $markStep);

        $firstRecordDay = clone $this->records[0]->getDate();

        $markWidth = ($width - $this->margin[0]) / $marksDuration;

        $offset = $markWidth * ($firstRecordDay->hour * 60 + $firstRecordDay->minute) / $markStep;

        for ($i = 0; $i <= $marksDuration; $i++) {
            $x = $markWidth * $i - $offset + $this->margin[0];

            if ($x < 0) {
                $firstRecordDay->addMinutes($markStep);

                continue;
            }

            $drawer->line(
                new Point($x, $height + $this->margin[1]),
                new Point($x, $this->margin[1]),
                $this->colors['mark'],
                5
            );

            $drawer->text(
                $firstRecordDay->format('m.d'),
                new Font($this->font, 20, $this->colors['text']),
                new Point($x + 5, $height + 80)
            );

            $firstRecordDay->addMinutes($markStep);
        }
    }

    /**
     * Draw chart marks
     *
     * @param DrawerInterface $drawer
     */
    private function drawValueMarks(DrawerInterface $drawer): void
    {
        $markStep = 15;

        $markHeight = $this->size[1] / $markStep;

        $markValue = round($this->getHighestValue() / $markStep);

        for ($j = 0; $j <= $markStep; $j++) {
            $y = ($this->size[1]) - $markHeight * $j + $this->margin[1];

            if ($y < 0) {
                continue;
            }

            $drawer->line(
                new Point($this->margin[0], $y),
                new Point($this->size[0], $y),
                $this->colors['mark'],
                5
            );

            $drawer->text(
                $markValue * $j,
                new Font($this->font, 20, $this->colors['text']),
                new Point($this->margin[0], $y + 5)
            );
        }
    }

    /**
     * Get highest value from the array of records
     *
     * @return int
     */
    public function getHighestValue(): int
    {
        //TODO: Save the highest number for better performance

        $highest = 0;

        foreach ($this->records as $record) {
            if ($highest < $value = $record->getValue()) {
                $highest = $value;
            }
        }

        return $highest;
    }

    /**
     * Draw chart
     *
     * @param DrawerInterface $drawer
     */
    private function drawChart(DrawerInterface $drawer): void
    {
        $records = $this->records;
        $startPoint = new Point($this->margin[0], $this->calcY($records[0]));

        $points = [$startPoint];

        unset($records[0]);

        foreach ($records as $record) {
            $endPoint = $this->calcPoint($record);

            $drawer->line($startPoint, $endPoint, $this->colors['line'], 10);

            $points[] = $endPoint;

            $startPoint = $endPoint;
        }


        foreach ($points as $point) {
            $drawer->circle($point, 10, $this->colors['point'], true);
        }
    }

    /**
     * Calculate Y position for the record on chart
     *
     * @param ChartRecord $record
     * @return float|int
     */
    public function calcY(ChartRecord $record)
    {
        return $this->size[1] - ($this->size[1] / $this->getHighestValue() * $record->getValue()) + $this->margin[1];
    }

    /**
     * Calculate X position for the record on chart
     *
     * @param ChartRecord $record
     * @return float|int
     */
    public function calcX(ChartRecord $record)
    {
        return ($this->size[0] - $this->margin[0]) / $this->duration * $this->diff($this->records[0], $record) + $this->margin[0];
    }

    /**
     * Calculate point for the record on chart
     *
     * @param ChartRecord $record
     * @return Point
     */
    public function calcPoint(ChartRecord $record): Point
    {
        return new Point($this->calcX($record), $this->calcY($record));
    }
}
