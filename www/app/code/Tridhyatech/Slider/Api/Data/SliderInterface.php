<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace  Tridhyatech\Slider\Api\Data;

interface SliderInterface
{

    const SLIDERID = 'slider_id';
    const TITLE = 'title';
    const SLIDERURL = 'sliderurl';
    const ICON = 'icon';
    const VIDEO = 'video';
    const DISPLAYON = 'displayon';
    const SORTORDER = 'sortorder';
    const STATUS = 'status';
    const CONTENT = 'content';
    const RIGHTCONTENT = 'rightcontent';
    const STOREVIEWS ="store_id";
    const CMS = 'cms';
    const PRODUCTS = 'products';



    public function getSliderId();

    public function setSliderId($sliderId);


    public function getTitle();

    public function setTitle($title);


    public function getSliderurl();

    public function setSliderurl($sliderurl);


    public function getIcon();

    public function setIcon($icon);


    public function getVideo();

    public function setVideo($video);


    public function getDisplayon();

    public function setDisplayon($displayon);


    public function getStatus();

    public function setStatus($status);


    public function getSortorder();

    public function setSortorder($sortorder);


    public function getContent();

    public function setContent($content);


    public function getRightcontent();

    public function setRightcontent($rightcontent);


    public function getStoreviews();

    public function setStoreviews($storeviews);


    public function getCms();

    public function setCms($cms);


    public function getProducts();

    public function setProducts($products);


}
