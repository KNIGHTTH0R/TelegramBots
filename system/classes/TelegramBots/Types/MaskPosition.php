<?php


namespace TelegramBots\Types;


class MaskPosition implements TypeInterface
{

    /**
     * @var string
     */
    public $point;

    /**
     * @var float
     */
    public $x_shift;

    /**
     * @var float
     */
    public $y_shift;

    /**
     * @var float
     */
    public $scale;

    /**
     * MaskPosition constructor.
     * @param string $point
     * @param float $x_shift
     * @param float $y_shift
     * @param float $scale
     */
    public function __construct(string $point, float $x_shift, float $y_shift, float $scale)
    {
        $this->point = $point;
        $this->x_shift = $x_shift;
        $this->y_shift = $y_shift;
        $this->scale = $scale;
    }

    public function getRequestArray(): array
    {
        return [
            'point' => $this->point,
            'x_shift' => $this->x_shift,
            'y_shift' => $this->y_shift,
            'scale' => $this->scale,
        ];
    }

}