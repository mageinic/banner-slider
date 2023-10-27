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
define([
    'jquery',
    'jquery-ui-modules/widget',
    'slick'
], function ($) {
    'use strict';

    return function (config, sliderElement) {
        var $element = $(sliderElement);

        $(".mageinicslider-container").on('init', function (event, slick) {
            var animationClass = $('.slick-active').find('.banner-caption').attr('data-animate');
            $(this).find('.animated').addClass('activate ' + animationClass);
        });

        $('.mageinicslider-container').not('.slick-initialized').slick({
            arrows: config.arrows,
            dots: config.dots,
            infinite: config.infinite,
            autoplay: config.autoplay,
            autoplaySpeed: config.autoplaySpeed ? config.autoplaySpeed : 2000,
            slidesToShow: config.slidesToShow ? config.slidesToShow : 1,
            slidesToScroll: config.slidesToScroll ? config.slidesToScroll : 1,
            speed: config.speed ? config.speed : 400,
            responsive: config.responsive,
        });

        $('.mageinicslider-container-nav').not('.slick-initialized').slick({
            infinite: config.infinite,
            slidesToShow: 3,
            slidesToScroll: 1,
            asNavFor: '.mageinicslider-container',
            focusOnSelect: true,
            arrows: false
        });

        $(".mageinicslider-container").on('afterChange', function (event, slick, currentSlide) {
            var animationClass = $('.slick-active').find('.banner-caption').attr('data-animate');
            $(currentSlide).find('.banner-caption').addClass('animated ' + animationClass);
            $('.animated').removeClass('off');
            $('.animated').addClass('activate ' + animationClass);
        });

        $(".mageinicslider-container").on('beforeChange', function (event, slick, currentSlide) {
            var animationClass = $('.slick-active').find('.banner-caption').attr('data-animate');
            $(currentSlide).find('.banner-caption').addClass('animated ' + animationClass);
            $('.animated').removeClass('activate ' + animationClass);
            $('.animated').addClass('off');
        });
    }
});
