<?php
namespace Magento\Catalog\Api\Data;

/**
 * Extension class for @see \Magento\Catalog\Api\Data\ProductRenderInterface
 */
class ProductRenderExtension extends \Magento\Framework\Api\AbstractSimpleObject implements ProductRenderExtensionInterface
{
    /**
     * @return string|null
     */
    public function getReviewHtml()
    {
        return $this->_get('review_html');
    }

    /**
     * @param string $reviewHtml
     * @return $this
     */
    public function setReviewHtml($reviewHtml)
    {
        $this->setData('review_html', $reviewHtml);
        return $this;
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductRender\ButtonInterface|null
     */
    public function getWishlistButton()
    {
        return $this->_get('wishlist_button');
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductRender\ButtonInterface $wishlistButton
     * @return $this
     */
    public function setWishlistButton(\Magento\Catalog\Api\Data\ProductRender\ButtonInterface $wishlistButton)
    {
        $this->setData('wishlist_button', $wishlistButton);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDdgBrand()
    {
        return $this->_get('ddg_brand');
    }

    /**
     * @param string $ddgBrand
     * @return $this
     */
    public function setDdgBrand($ddgBrand)
    {
        $this->setData('ddg_brand', $ddgBrand);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDdgDescription()
    {
        return $this->_get('ddg_description');
    }

    /**
     * @param string $ddgDescription
     * @return $this
     */
    public function setDdgDescription($ddgDescription)
    {
        $this->setData('ddg_description', $ddgDescription);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDdgSku()
    {
        return $this->_get('ddg_sku');
    }

    /**
     * @param string $ddgSku
     * @return $this
     */
    public function setDdgSku($ddgSku)
    {
        $this->setData('ddg_sku', $ddgSku);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDdgImage()
    {
        return $this->_get('ddg_image');
    }

    /**
     * @param string $ddgImage
     * @return $this
     */
    public function setDdgImage($ddgImage)
    {
        $this->setData('ddg_image', $ddgImage);
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getDdgCategories()
    {
        return $this->_get('ddg_categories');
    }

    /**
     * @param string[] $ddgCategories
     * @return $this
     */
    public function setDdgCategories($ddgCategories)
    {
        $this->setData('ddg_categories', $ddgCategories);
        return $this;
    }
}
