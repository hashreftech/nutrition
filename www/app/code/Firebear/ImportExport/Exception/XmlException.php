<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Exception;

use Magento\Framework\Phrase;
use Magento\Framework\Phrase\Renderer\Placeholder;

/**
 * Class XmlException
 *
 * @package Firebear\ImportExport\Exception
 */
class XmlException extends \Magento\Framework\Exception\LocalizedException
{

    /**
     * @var string
     */
    private $errorMessage = "";

    /**
     * XmlException constructor.
     * @param array $errors
     */
    public function __construct($errors = [])
    {
        $x = 0;
        foreach ($errors as $error) {
            if ($error instanceof \LibXMLError) {
                $this->parseError($error);
                $x++;
            }
        }
        if ($x > 0) {
            parent::__construct(__($this->errorMessage));
        } else {
            parent::__construct(__("Unknown Error XmlException"));
        }
    }

    /**
     * @param \LibXMLError $error
     */
    public function parseError(\LibXMLError $error)
    {
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $this->errorMessage .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $this->errorMessage .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $this->errorMessage .= "Fatal Error $error->code: ";
                break;
        }

        $this->errorMessage .= trim($error->message) . "\n  Line: $error->line" . "\n  Column: $error->column";

        if ($error->file) {
            $this->errorMessage .= "\n  File: $error->file";
        }
    }
}
