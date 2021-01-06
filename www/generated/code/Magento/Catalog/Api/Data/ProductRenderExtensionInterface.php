<?php
namespace Magento\Catalog\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Catalog\Api\Data\ProductRenderInterface
 */
interface ProductRenderExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return string|null
     */
    public function getReviewHtml();

    /**
     * @param string $reviewHtml
     * @return $this
     */
    public function setReviewHtml($reviewHtml);

    /**
     * @return \Magento\Catalog\Api\Data\ProductRender\ButtonInterface|null
     */
    public function getWishlistButton();

    /**
     * @param \Magento\Catalog\Api\Data\ProductRender\ButtonInterface $wishlistButton
     * @return $this
     */
    public function setWishlistButton(\Magento\Catalog\Api\Data\ProductRender\ButtonInterface $wishlistButton);

    /**
     * @return string|null
     */
    public function getDdgBrand();

    /**
     * @param string $ddgBrand
     * @return $this
     */
    public function setDdgBrand($ddgBrand);

    /**
     * @return string|null
     */
    public function getDdgDescription();

    /**
     * @param string $ddgDescription
     * @return $this
     */
    public function setDdgDescription($ddgDescription);

    /**
     * @return string|null
     */
    public function getDdgSku();

    /**
     * @param string $ddgSku
     * @return $this
     */
    public function setDdgSku($ddgSku);

    /**
     * @return string|null
     */
    public function getDdgImage();

    /**
     * @param string $ddgImage
     * @return $this
     */
    public function setDdgImage($ddgImage);

    /**
     * @return string[]|null
     */
    public function getDdgCategories();

    /**
     * @param string[] $ddgCategories
     * @return $this
     */
    public function setDdgCategories($ddgCategories);
}
