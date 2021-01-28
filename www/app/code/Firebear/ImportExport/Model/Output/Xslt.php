<?php

/*
 * @copyright: Copyright © 2018 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Output;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Xslt
 *
 * @package Firebear\ImportExport\Model\Output
 */
class Xslt
{
    /**
     * @param $file
     * @param $xsl
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function convert($file, $xsl)
    {
        if (!class_exists('\XSLTProcessor')) {
            throw new LocalizedException(__(
                'The XSLTProcessor class could not be found. This means your PHP installation is missing XSL features.'
            ));
        }
        $xmlDoc = new \DOMDocument();

        $xmlDoc->loadXML($file, LIBXML_COMPACT | LIBXML_PARSEHUGE | LIBXML_NOWARNING);

        $xslDoc = new \DOMDocument();
        $xslDoc->loadXML($xsl, LIBXML_COMPACT | LIBXML_PARSEHUGE | LIBXML_NOWARNING);

        $proc = new \XSLTProcessor();
        $proc->registerPHPFunctions();
        $proc->importStylesheet($xslDoc);
        try {
            $newDom = $proc->transformToDoc($xmlDoc);
        } catch (\Exception $e) {
            throw new LocalizedException(__("Error : " . $e->getMessage()));
        }

        return $newDom->saveXML();
    }
}
