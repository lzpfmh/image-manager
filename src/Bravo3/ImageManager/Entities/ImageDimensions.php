<?php

namespace Bravo3\ImageManager\Entities;

use Bravo3\ImageManager\Entities\Interfaces\SerialisableInterface;

/**
 * A set of rules for resampling images.
 */
class ImageDimensions implements SerialisableInterface
{
    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var bool
     */
    protected $upscale;

    /**
     * @var bool
     */
    protected $maintain_ratio;

    /**
     * @var bool
     */
    protected $grab;

    /**
     * @param int  $width
     * @param int  $height
     * @param bool $maintain_ratio
     * @param bool $upscale
     * @param bool $grab
     */
    public function __construct($width = null, $height = null,
        $maintain_ratio = true, $upscale = true, $grab = false)
    {
        $this->width          = $width;
        $this->height         = $height;
        $this->upscale        = $upscale;
        $this->maintain_ratio = $maintain_ratio;
        $this->grab           = $grab;
    }

    /**
     * Creates a signature containing the dimension specification.
     *
     * @return string
     */
    public function __toString()
    {
        return 'x'.($this->getWidth() ?: '-').
               'y'.($this->getHeight() ?: '-').
               'u'.($this->canUpscale() ? '1' : '0').
               'r'.($this->getMaintainRatio() ? '1' : '0').
               'g'.($this->getGrab() ? 'g' : '0');
    }

    /**
     * Get proposed width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get proposed height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get image aspect ratio based on the width and height
     * provided.
     *
     * Function uses binary calculator division to 3 decimal places.
     *
     * @return string
     */
    public function getAspectRatio()
    {
        return bcdiv($this->width, $this->height, 3);
    }

    /**
     * Check if the image can be upscaled.
     *
     * @return bool
     */
    public function canUpscale()
    {
        return $this->upscale;
    }

    /**
     * Check if we should maintain the image ratio.
     *
     * @return bool
     */
    public function getMaintainRatio()
    {
        return $this->maintain_ratio;
    }

    /**
     * Check if also crop as well as resize.
     *
     * @return bool
     */
    public function getGrab()
    {
        return $this->grab;
    }

    /**
     * {@inheritdoc}
     */
    public function serialise()
    {
        return json_encode([
            'width'          => $this->getWidth(),
            'height'         => $this->getHeight(),
            'upscale'        => (bool) $this->canUpscale(),
            'grab'           => (bool) $this->getGrab(),
            'maintain-ratio' => (bool) $this->getMaintainRatio(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialise($json)
    {
        if (empty($json)) {
            throw \InvalidArgumentException('Json string is empty');
        }

        $object_data = json_decode($json, true);

        $instance = new static(
            isset($object_data['width']) ? $object_data['width'] : null,
            isset($object_data['height']) ? $object_data['height'] : null,
            isset($object_data['maintain-ratio']) ? $object_data['maintain-ratio'] : null,
            isset($object_data['upscale']) ? $object_data['upscale'] : null,
            isset($object_data['grab']) ? $object_data['grab'] : null
        );

        return $instance;
    }
}
