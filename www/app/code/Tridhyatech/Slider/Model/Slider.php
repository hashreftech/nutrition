<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Model;

use Tridhyatech\Slider\Api\Data\SliderInterface;

class Slider extends \Magento\Framework\Model\AbstractModel implements SliderInterface
{


    public function _construct()
    {
        $this->_init('Tridhyatech\Slider\Model\ResourceModel\Grid');
    }


    public function getSliderId()
    {
        return $this->getData(self::SLIDERID);
    }


    public function setSliderId($sliderId)
    {
        return $this->setData(self::SLIDERID, $sliderId);
    }


    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }


    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }


    public function getSliderurl()
    {
        return $this->getData(self::SLIDERURL);
    }


    public function setSliderurl($sliderurl)
    {
        return $this->setData(self::SLIDERURL, $sliderurl);
    }


    public function getIcon()
    {
        return $this->getData(self::ICON);
    }


    public function setIcon($icon)
    {
        return $this->setData(self::ICON, $icon);
    }


    public function getVideo()
    {
        return $this->getData(self::VIDEO);
    }


    public function setVideo($video)
    {
        return $this->setData(self::VIDEO, $video);
    }


    public function getDisplayon()
    {
        return $this->getData(self::DISPLAYON);
    }

    public function setDisplayon($displayon)
    {
        return $this->setData(self::DISPLAYON, $displayon);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }


    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
    public function getSortorder()
    {
        return $this->getData(self::SORTORDER);
    }


    public function setSortorder($sortorder)
    {
        return $this->setData(self::SORTORDER, $sortorder);
    }

    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }


    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    public function getRightcontent()
    {
        return $this->getData(self::RIGHTCONTENT);
    }


    public function setRightcontent($rightcontent)
    {
        return $this->setData(self::RIGHTCONTENT, $rightcontent);
    }

    public function getStoreviews()
    {
        return $this->getData(self::STOREVIEWS);
    }


    public function setStoreviews($storeviews)
    {
        return $this->setData(self::STOREVIEWS, $storeviews);
    }
    public function getCms()
    {
        return $this->getData(self::CMS);
    }


    public function setCms($cms)
    {
        return $this->setData(self::CMS, $cms);
    }
    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
    }


    public function setProducts($products)
    {
        return $this->setData(self::PRODUCTS, $products);
    }
}
