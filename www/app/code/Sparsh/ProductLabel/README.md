##Product Label Extension

This extension is used to highlight your products using different custom labels like "New", "Best Seller", "Most Popular", "Sale" etc...

##Support: 
version - 2.3.x, 2.4.x

##How to install Extension

1. Download the archive file.
2. Unzip the file
3. Create a folder [Magento_Root]/app/code/Sparsh/ProductLabel
4. Drop/move the unzipped files to directory '[Magento_Root]/app/code/Sparsh/ProductLabel'

#Enable Extension:
- php bin/magento module:enable Sparsh_ProductLabel
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Sparsh_ProductLabel
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush
