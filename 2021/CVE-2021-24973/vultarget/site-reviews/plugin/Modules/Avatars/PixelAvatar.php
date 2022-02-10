<?php

/**
 * Adapted from: https://github.com/BinaryMoon/wp-toolbelt/tree/master/modules/avatars
 * @see: https://www.binarymoon.co.uk/2020/08/pixel-avatars-a-privacy-first-gravatar-replacement/
 */

namespace GeminiLabs\SiteReviews\Modules\Avatars;

class PixelAvatar extends SvgAvatar
{
    const HEIGHT = 11;
    const WIDTH = 11;

    /**
     * @var array
     */
    public $data;

    /**
     * @var string
     */
    public $hash;

    /**
     * @var int
     */
    public $hashIndex = 0;

    /**
     * @var array
     */
    public $pixels = [
        'palette' => [
            'all' => [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200, 210, 215, 220, 230, 240, 250, 260, 270, 280, 290, 300, 310, 320, 330, 340, 350],
            'skin' => [60, 80, 100, 120, 140, 160, 180, 220, 240, 280, 300, 320, 340],
        ],
        'face' => [
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 0, 1, 0, 0, 2, 0, 0, 1, 0, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 2, 1, 1, 9, 1, 9, 1, 1, 2, 0],
                [0, 2, 1, 1, 1, 1, 1, 1, 1, 2, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 1, 9, 1, 9, 1, 2, 0, 0],
                [0, 0, 2, 1, 1, 1, 1, 1, 2, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
        ],
        'mouth' => [
            [1, 1, 1],
            [1, 1, 1],
            [1, 1, 1],
            [0, 1, 0],
            [0, 1, 1],
            [1, 1, 0],
        ],
        'body' => [
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            ],
            [
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 1, 1, 2, 1, 1, 1, 2, 1, 1, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            ],
            [
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 1, 2, 2, 2, 1, 0, 0, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 2, 1, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 2, 1, 2, 1, 2, 1, 0, 0],
                [0, 0, 1, 2, 1, 2, 1, 2, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 2, 9, 2, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 9, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 1, 9, 1, 1, 1, 0, 0],
            ],
        ],
        'hair' => [
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 2, 2, 2, 2, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 2, 0, 0, 0, 2, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 2, 2, 0, 2, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 2, 2, 0, 2, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 2, 1, 2, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 2, 2, 2, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 0, 2, 2, 2, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 2, 0, 0, 0, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 2, 1, 2, 0, 0, 0, 0],
                [0, 0, 0, 1, 2, 1, 2, 1, 0, 0, 0],
                [0, 0, 0, 2, 2, 2, 2, 2, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 2, 2, 2, 1, 0, 0, 0],
                [0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 2, 2, 2, 0, 0, 0, 0],
                [0, 0, 0, 1, 2, 2, 2, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0],
                [0, 0, 1, 0, 1, 0, 1, 0, 1, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
            ],
            [
                [0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 2, 2, 2, 0, 0, 0, 0],
                [0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 2, 2, 2, 2, 2, 2, 0, 0],
            ],
            [
                [0, 0, 0, 8, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 8, 1, 1, 1, 0, 0, 0],
                [0, 0, 2, 2, 2, 2, 2, 2, 2, 0, 0],
            ],
            [
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 1, 1, 1, 2, 2, 2, 1, 1, 1, 0],
                [0, 1, 1, 2, 0, 0, 0, 2, 1, 1, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
                [0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0],
                [0, 0, 1, 2, 2, 1, 2, 2, 1, 0, 0],
                [0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 1, 2, 2, 2, 2, 2, 1, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],
                [0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 2, 2, 2, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 2, 2, 2, 1, 1, 0, 0],
                [0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
                [0, 0, 1, 2, 0, 0, 0, 2, 1, 0, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
                [0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0],
            ],
            [
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                [0, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
                [0, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0],
            ],
        ],
    ];

    /**
     * @param string $from
     * @return string
     */
    public function generate($from)
    {
        $this->data = $this->newData();
        $this->hash = $this->filename($from);
        $this->hashIndex = 0;
        $this->addBody();
        $this->addFace();
        $this->addMouth();
        $this->addHair();
        return $this->draw();
    }

    /**
     * @return void
     */
    protected function addBody()
    {
        $color = $this->getColor(50, 45);
        $pixels = $this->getPixels('body');
        $yOffset = 8;
        for ($y = 0; $y < static::HEIGHT - $yOffset; ++$y) {
            for ($x = 0; $x < static::WIDTH; ++$x) {
                $pixelColor = $this->setPixelColour($pixels[$y][$x], $this->data[$y + $yOffset][$x], $color);
                $this->data[$y + $yOffset][$x] = $pixelColor;
            }
        }
    }

    /**
     * @return void
     */
    protected function addFace()
    {
        $color = $this->getColor(40, 65, 'skin');
        $pixels = $this->getPixels('face');
        $numPixels = count($pixels);
        $yOffset = 3;
        for ($y = 0; $y < $numPixels; ++$y) {
            for ($x = 0; $x < static::WIDTH; ++$x) {
                $pixelColor = $this->setPixelColour($pixels[$y][$x], $this->data[$y + $yOffset][$x], $color);
                $this->data[$y + $yOffset][$x] = $pixelColor;
            }
        }
    }

    /**
     * @return void
     */
    protected function addHair()
    {
        $color = $this->getColor(70, 45);
        $pixels = $this->getPixels('hair');
        $numPixels = count($pixels);
        for ($y = 0; $y < $numPixels; ++$y) {
            for ($x = 0; $x < static::WIDTH; ++$x) {
                $pixelColor = $this->setPixelColour($pixels[$y][$x], $this->data[$y][$x], $color);
                $this->data[$y][$x] = $pixelColor;
            }
        }
    }

    /**
     * @return void
     */
    protected function addMouth()
    {
        $color = $this->getColor(60, 30);
        $pixels = $this->getPixels('mouth');
        if (1 === $pixels[0]) {
            $this->data[6][4] = $color[0];
        }
        if (1 === $pixels[1]) {
            $this->data[6][5] = $color[0];
        }
        if (1 === $pixels[2]) {
            $this->data[6][6] = $color[0];
        }
    }

    /**
     * @return string
     */
    protected function draw()
    {
        $paths = [];
        $background = $this->getColor(85, 85);
        $commands = [];
        $commands[$background[0]] = sprintf('M0 0h%dv%dH0z', static::WIDTH, static::HEIGHT);
        for ($y = 0; $y < static::HEIGHT; ++$y) {
            for ($x = 0; $x < static::WIDTH; ++$x) {
                if ($fill = $this->data[$y][$x]) {
                    if (!isset($commands[$fill])) {
                        $commands[$fill] = '';
                    }
                    $commands[$fill] .= sprintf('M%d %dh1v1H%dz', $x, $y, $x);
                }
            }
        }
        foreach ($commands as $fill => $d) {
            $paths[] = sprintf('<path fill="%s" d="%s"/>', $fill, $d);
        }
        return sprintf('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d">%s</svg>',
            static::WIDTH,
            static::HEIGHT,
            implode('', $paths)
        );
    }

    /**
     * @param string $from
     * @return string
     */
    protected function filename($from)
    {
        $hash = md5(strtolower(trim($from)));
        $hash = substr($hash, 0, 15);
        return $hash;
    }

    /**
     * @param int $saturation
     * @param int $lightness
     * @param string $paletteKey
     * @return array
     * @see https://www.w3schools.com/colors/colors_hsl.asp
     */
    protected function getColor($saturation, $lightness, $paletteKey = 'all')
    {
        $palette = $this->pixels['palette'][$paletteKey];
        $index = $this->stringVal() % count($palette);
        $hue = $palette[$index];
        return [
            $this->hslToHex($hue, $saturation / 100, $lightness / 100),
            $this->hslToHex($hue, ($saturation + 10) / 100, ($lightness - 20) / 100),
        ];
    }

    /**
     * @param string $name
     * @return array
     */
    protected function getPixels($name)
    {
        $index = $this->stringVal() % count($this->pixels[$name]);
        return $this->pixels[$name][$index];
    }

    /**
     * @param float $hue
     * @param float $saturation
     * @param float $lightness
     * @return string
     */
    protected function hslToHex($hue, $saturation, $lightness)
    {
        $hue = $hue / 360;
        if (0 == $saturation) {
            $red = $green = $blue = $lightness; // achromatic
        } else {
            if ($lightness < 0.5) {
                $v2 = $lightness * (1 + $saturation);
            } else {
                $v2 = ($lightness + $saturation) - ($lightness * $saturation);
            }
            $v1 = 2 * $lightness - $v2;
            $red = $this->hueToRgb($v1, $v2, $hue + (1 / 3));
            $green = $this->hueToRgb($v1, $v2, $hue);
            $blue = $this->hueToRgb($v1, $v2, $hue - (1 / 3));
        }
        $red = (int) round($red * 255);
        $green = (int) round($green * 255);
        $blue = (int) round($blue * 255);
        return '#'.str_pad(dechex(($red << 16) + ($green << 8) + $blue), 6, '0', STR_PAD_LEFT);
    }

    /**
     * @param float $v1
     * @param float $v2
     * @param float $vH
     * @return float
     */
    protected function hueToRgb($v1, $v2, $vH)
    {
        if ($vH < 0) {
            ++$vH;
        }
        if ($vH > 1) {
            --$vH;
        }
        if ($vH < (1 / 6)) {
            return $v1 + ($v2 - $v1) * 6 * $vH;
        }
        if ($vH < (1 / 2)) {
            return $v2;
        }
        if ($vH < (2 / 3)) {
            return $v1 + ($v2 - $v1) * ((2 / 3) - $vH) * 6;
        }
        return $v1;
    }

    /**
     * @return array
     */
    protected function newData()
    {
        $data = [];
        for ($y = 0; $y < static::HEIGHT; ++$y) {
            $data[$y] = [];
            for ($x = 0; $x < static::WIDTH; ++$x) {
                $data[$y][$x] = null;
            }
        }
        return $data;
    }

    /**
     * @param int $pixel
     * @param string $current
     * @param array $palette
     * @return string
     */
    protected function setPixelColour($pixel, $current, $palette)
    {
        $color = $current;
        switch ($pixel) {
            case 1:
                $color = $palette[0];
                break;
            case 2:
                $color = $palette[1];
                break;
            case 8:
                $color = '#fff';
                break;
            case 9:
                $color = '#000';
                break;
        }
        return $color;
    }

    /**
     * Get the value of the next character in the hash.
     * @return int
     */
    protected function stringVal()
    {
        ++$this->hashIndex;
        $this->hashIndex = $this->hashIndex % strlen($this->hash);
        return ord($this->hash[$this->hashIndex]) + (ord("\0") << 8);
    }
}
