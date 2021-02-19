<?php

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'MGS_Brand',
    __DIR__
);
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Observer/Frontend/License/License.php')) {
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Observer/Frontend/License/License.php');
}
