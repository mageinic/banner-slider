<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_BannerSlider
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\BannerSlider\Model\Config\Source;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Option\ArrayInterface;

/**
 * Banner Slider Animations
 */
class Animations implements ArrayInterface
{
    public const CAPTION_ANIMATION_BOUNCE = 'bounce';
    public const CAPTION_ANIMATION_FLASH = 'flash';
    public const CAPTION_ANIMATION_PULSE = 'pulse';
    public const CAPTION_ANIMATION_RUBBER_BAND = 'rubberBand';
    public const CAPTION_ANIMATION_SHAKE = 'shake';
    public const CAPTION_ANIMATION_SWING = 'swing';
    public const CAPTION_ANIMATION_TADA = 'tada';
    public const CAPTION_ANIMATION_WOBBLE = 'wobble';
    public const CAPTION_ANIMATION_JELLO = 'jello';
    public const CAPTION_ANIMATION_BOUNCE_IN = 'bounceIn';
    public const CAPTION_ANIMATION_BOUNCE_IN_DOWN = 'bounceInDown';
    public const CAPTION_ANIMATION_BOUNCE_IN_LEFT = 'bounceInLeft';
    public const CAPTION_ANIMATION_BOUNCE_IN_RIGHT = 'bounceInRight';
    public const CAPTION_ANIMATION_BOUNCE_IN_UP = 'bounceInUp';
    public const CAPTION_ANIMATION_BOUNCE_OUT = 'bounceOut';
    public const CAPTION_ANIMATION_BOUNCE_OUT_DOWN = 'bounceOutDown';
    public const CAPTION_ANIMATION_BOUNCE_OUT_LEFT = 'bounceOutLeft';
    public const CAPTION_ANIMATION_BOUNCE_OUT_RIGHT = 'bounceOutRight';
    public const CAPTION_ANIMATION_BOUNCE_OUT_UP = 'bounceOutUp';
    public const CAPTION_ANIMATION_FADE_IN = 'fadeIn';
    public const CAPTION_ANIMATION_FADE_IN_DOWN = 'fadeInDown';
    public const CAPTION_ANIMATION_FADE_IN_BIG = 'fadeInDownBig';
    public const CAPTION_ANIMATION_FADE_IN_LEFT = 'fadeInLeft';
    public const CAPTION_ANIMATION_FADE_IN_LEFT_BIG = 'fadeInLeftBig';
    public const CAPTION_ANIMATION_FADE_IN_RIGHT = 'fadeInRight';
    public const CAPTION_ANIMATION_FADE_IN_RIGHT_BIG = 'fadeInRightBig';
    public const CAPTION_ANIMATION_FADE_IN_UP = 'fadeInUp';
    public const CAPTION_ANIMATION_FADE_IN_UP_BIG = 'fadeInUpBig';
    public const CAPTION_ANIMATION_FADE_OUT = 'fadeOut';
    public const CAPTION_ANIMATION_FADE_OUT_DOWN = 'fadeOutDown';
    public const CAPTION_ANIMATION_FADE_OUT_DOWN_BIG = 'fadeOutDownBig';
    public const CAPTION_ANIMATION_FADE_OUT_LEFT = 'fadeOutLeft';
    public const CAPTION_ANIMATION_FADE_OUT_LEFT_BIG = 'fadeOutLeftBig';
    public const CAPTION_ANIMATION_FADE_OUT_RIGHT = 'fadeOutRight';
    public const CAPTION_ANIMATION_FADE_OUT_RIGHT_BIG = 'fadeOutRightBig';
    public const CAPTION_ANIMATION_FADE_OUT_UP = 'fadeOutUp';
    public const CAPTION_ANIMATION_FADE_OUT_UP_BIG = 'fadeOutUpBig';
    public const CAPTION_ANIMATION_FLIP = 'flip';
    public const CAPTION_ANIMATION_FLIP_INX = 'flipInX';
    public const CAPTION_ANIMATION_FLIP_INY = 'flipInY';
    public const CAPTION_ANIMATION_FLIP_OUTX = 'flipOutX';
    public const CAPTION_ANIMATION_FLIP_OUTY = 'flipOutY';
    public const CAPTION_ANIMATION_LIGHT_SPEED_IN = 'lightSpeedIn';
    public const CAPTION_ANIMATION_LIGHT_SPEED_OUT = 'lightSpeedOut';
    public const CAPTION_ANIMATION_ROTATE_IN = 'rotateIn';
    public const CAPTION_ANIMATION_ROTATE_IN_DOWN_LEFT = 'rotateInDownLeft';
    public const CAPTION_ANIMATION_ROTATE_IN_DOWN_RIGHT = 'rotateInDownRight';
    public const CAPTION_ANIMATION_ROTATE_IN_UP_LEFT = 'rotateInUpLeft';
    public const CAPTION_ANIMATION_ROTATE_IN_UP_RIGHT = 'rotateInUpRight';
    public const CAPTION_ANIMATION_ROTATE_OUT = 'rotateOut';
    public const CAPTION_ANIMATION_ROTATE_OUTDOWN_LEFT = 'rotateOutDownLeft';
    public const CAPTION_ANIMATION_ROTATE_OUTDOWN_RIGHT = 'rotateOutDownRight';
    public const CAPTION_ANIMATION_ROTATE_OUT_UP_LEFT = 'rotateOutUpLeft';
    public const CAPTION_ANIMATION_ROTATE_OUT_UP_RIGHT = 'rotateOutUpRight';
    public const CAPTION_ANIMATION_SLIDE_IN_UP = 'slideInUp';
    public const CAPTION_ANIMATION_SLIDE_IN_DOWN = 'slideInDown';
    public const CAPTION_ANIMATION_SLIDE_IN_LEFT = 'slideInLeft';
    public const CAPTION_ANIMATION_SLIDE_IN_RIGHT = 'slideInRight';
    public const CAPTION_ANIMATION_SLIDE_OUT_UP = 'slideOutUp';
    public const CAPTION_ANIMATION_SLIDE_OUT_DOWN = 'slideOutDown';
    public const CAPTION_ANIMATION_SLIDE_OUT_LEFT = 'slideOutLeft';
    public const CAPTION_ANIMATION_SLIDE_OUT_RIGHT = 'slideOutRight';
    public const CAPTION_ANIMATION_ZOOM_IN = 'zoomIn';
    public const CAPTION_ANIMATION_ZOOM_IN_DOWN = 'zoomInDown';
    public const CAPTION_ANIMATION_ZOOM_IN_LEFT = 'zoomInLeft';
    public const CAPTION_ANIMATION_ZOOM_IN_RIGHT = 'zoomInRight';
    public const CAPTION_ANIMATION_ZOOM_IN_UP = 'zoomInUp';
    public const CAPTION_ANIMATION_ZOOM_OUT = 'zoomOut';
    public const CAPTION_ANIMATION_ZOOM_OUT_DOWN = 'zoomOutDown';
    public const CAPTION_ANIMATION_ZOOM_OUT_LEFT = 'zoomOutLeft';
    public const CAPTION_ANIMATION_ZOOM_OUT_RIGHT = 'zoomOutRight';
    public const CAPTION_ANIMATION_ZOOM_OUT_UP = 'zoomOutUp';
    public const CAPTION_ANIMATION_HINGE = 'hinge';
    public const CAPTION_ANIMATION_JACK_IN_THE_BOX = 'jackInTheBox';
    public const CAPTION_ANIMATION_ROLL_IN = 'rollIn';
    public const CAPTION_ANIMATION_ROLL_OUT = 'rollOut';

    /**
     * @var ModuleManager
     */
    protected ModuleManager $moduleManager;

    /**
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ModuleManager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Return array of sliders
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if (!$this->moduleManager->isEnabled('MageINIC_BannerSlider')) {
            return [];
        }
        $animations = [
            [
                'value' => ' ',
                'label' => __('Please Select')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE,
                'label' => __('bounce')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FLASH,
                'label' => __('flash')
            ],
            [
                'value' => self::CAPTION_ANIMATION_PULSE,
                'label' => __('pulse')
            ],
            [
                'value' => self::CAPTION_ANIMATION_RUBBER_BAND,
                'label' => __('rubberBand')
            ],
            [
                'value' => self::CAPTION_ANIMATION_SHAKE,
                'label' => __('shake')
            ],
            [
                'value' => self::CAPTION_ANIMATION_SWING,
                'label' => __('swing')
            ],
            [
                'value' => self::CAPTION_ANIMATION_WOBBLE,
                'label' => __('wobble')
            ],
            [
                'value' => self::CAPTION_ANIMATION_TADA,
                'label' => __('tada')
            ],
            [
                'value' => self::CAPTION_ANIMATION_JELLO,
                'label' => __('jello')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_IN,
                'label' => __('bounceIn')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_IN_DOWN,
                'label' => __('bounceInDown')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_IN_LEFT,
                'label' => __('bounceInLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_IN_RIGHT,
                'label' => __('bounceInRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_IN_UP,
                'label' => __('bounceInUp')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_OUT,
                'label' => __('bounceOut')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_OUT_LEFT,
                'label' => __('bounceOutLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_OUT_DOWN,
                'label' => __('bounceOutDown')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_OUT_RIGHT,
                'label' => __('bounceOutRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_BOUNCE_OUT_UP,
                'label' => __('bounceOutUp')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_IN,
                'label' => __('fadeIn')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_IN_DOWN,
                'label' => __('fadeInDown')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_IN_BIG,
                'label' => __('fadeInDownBig')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_IN_LEFT,
                'label' => __('fadeInLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_IN_LEFT_BIG,
                'label' => __('fadeInLeftBig')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_IN_RIGHT,
                'label' => __('fadeInRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_IN_RIGHT_BIG,
                'label' => __('fadeInRightBig')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_IN_UP,
                'label' => __('fadeInUp')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_IN_UP_BIG,
                'label' => __('fadeInUpBig')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_OUT,
                'label' => __('fadeOut')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_OUT_DOWN,
                'label' => __('fadeOutDown')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_OUT_DOWN_BIG,
                'label' => __('fadeOutDownBig')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_OUT_LEFT,
                'label' => __('fadeOutLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_OUT_LEFT_BIG,
                'label' => __('fadeOutLeftBig')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_OUT_RIGHT,
                'label' => __('fadeOutRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_OUT_RIGHT_BIG,
                'label' => __('fadeOutRightBig')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_OUT_UP,
                'label' => __('fadeOutUp')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FADE_OUT_UP_BIG,
                'label' => __('fadeOutUpBig')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FLIP,
                'label' => __('flip')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FLIP_INX,
                'label' => __('flipInX')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FLIP_INY,
                'label' => __('flipInY')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FLIP_OUTX,
                'label' => __('flipOutX')
            ],
            [
                'value' => self::CAPTION_ANIMATION_FLIP_OUTY,
                'label' => __('flipOutY')
            ],
            [
                'value' => self::CAPTION_ANIMATION_LIGHT_SPEED_IN,
                'label' => __('lightSpeedIn')
            ],
            [
                'value' => self::CAPTION_ANIMATION_LIGHT_SPEED_OUT,
                'label' => __('lightSpeedOut')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_IN,
                'label' => __('rotateIn')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_IN_DOWN_LEFT,
                'label' => __('rotateInDownLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_IN_DOWN_RIGHT,
                'label' => __('rotateInDownRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_IN_UP_LEFT,
                'label' => __('rotateInUpLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_IN_UP_RIGHT,
                'label' => __('rotateInUpRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_OUT,
                'label' => __('rotateOut')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_OUTDOWN_LEFT,
                'label' => __('rotateOutDownLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_OUTDOWN_RIGHT,
                'label' => __('rotateOutDownRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_OUT_UP_LEFT,
                'label' => __('rotateOutUpLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROTATE_OUT_UP_RIGHT,
                'label' => __('rotateOutUpRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_SLIDE_IN_UP,
                'label' => __('slideInUp')
            ],
            [
                'value' => self::CAPTION_ANIMATION_SLIDE_IN_DOWN,
                'label' => __('slideInDown')
            ],
            [
                'value' => self::CAPTION_ANIMATION_SLIDE_IN_LEFT,
                'label' => __('slideInLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_SLIDE_IN_RIGHT,
                'label' => __('slideInRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_SLIDE_OUT_UP,
                'label' => __('slideOutUp')
            ],

            [
                'value' => self::CAPTION_ANIMATION_SLIDE_OUT_DOWN,
                'label' => __('slideOutDown')
            ],
            [
                'value' => self::CAPTION_ANIMATION_SLIDE_OUT_LEFT,
                'label' => __('slideOutLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_SLIDE_OUT_RIGHT,
                'label' => __('slideOutRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_IN,
                'label' => __('zoomIn')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_IN_DOWN,
                'label' => __('zoomInDown')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_IN_LEFT,
                'label' => __('zoomInLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_IN_RIGHT,
                'label' => __('zoomInRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_IN_UP,
                'label' => __('zoomInUp')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_OUT,
                'label' => __('zoomOut')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_OUT_DOWN,
                'label' => __('zoomOutDown')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_OUT_LEFT,
                'label' => __('zoomOutLeft')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_OUT_RIGHT,
                'label' => __('zoomOutRight')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ZOOM_OUT_UP,
                'label' => __('zoomOutUp')
            ],
            [
                'value' => self::CAPTION_ANIMATION_HINGE,
                'label' => __('hinge')
            ],
            [
                'value' => self::CAPTION_ANIMATION_JACK_IN_THE_BOX,
                'label' => __('jackInTheBox')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROLL_IN,
                'label' => __('rollIn')
            ],
            [
                'value' => self::CAPTION_ANIMATION_ROLL_OUT,
                'label' => __('rollOut')
            ]
        ];
        return $animations;
    }
}
