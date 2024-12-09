## Magento 2 - Data Integrity Module
This module detects data integrity issues and gives solution options to solve them. It has crons that run to detect issues and clean up old ones.

## Preview
- Data integrity issues listing
  ![Issues Listing](readme-images/issues-listing.png?raw=true "Issues Listing")
- Issue details
  ![Issue Details](readme-images/issue-details.png?raw=true "Issues Listing")
- Apply bulk solutions
  ![Apply Bulk Solutions](readme-images/apply-bulk-solutions.png?raw=true "Issues Listing")

## Settings
There are no settings.

## Detectable Issues
- Products in single store mode shops that are not assigned to any website ([Test Readme](IntegrityTests/ProductWithoutWebsiteSingleStore/Test.md) | [Solution 1](IntegrityTests/ProductWithoutWebsiteSingleStore/Solutions/Solution1.md))
- URL rewrites in single store mode shops that have a wrong store ID ([Test Readme](IntegrityTests/UrlRewriteWrongStoreId/Test.md) | [Solution 1](IntegrityTests/UrlRewriteWrongStoreId/Solutions/Solution1.md) | [Solution 2](IntegrityTests/UrlRewriteWrongStoreId/Solutions/Solution2.md))
- Products with a URL path ([Test Readme](IntegrityTests/ProductWithUrlPath/Test.md) | [Solution 1](IntegrityTests/ProductWithUrlPath/Solutions/Solution1.md))
- SKU of products with white space at the beginning or end ([Test Readme](IntegrityTests/ProductWithWhiteSpaceSku/Test.md) | [Solution 1](IntegrityTests/ProductWithWhiteSpaceSku/Solutions/Solution1.md))

## Cli Commands
`data-integrity:issues:detect` - runs all data integrity test to detect issues on the shop.

## Developer information
Run the following command in Magento 2 root folder:

### Install module
1. Run `composer require salecto2/magento2-data-integrity`
2. Run `php bin/magento setup:upgrade`
3. Run `php bin/magento setup:di:compile`
4. Run `php bin/magento setup:static-content:deploy da_DK en_US`
5. Run `php bin/magento cache:clean`

### Uninstall module
1. Run `php bin/magento module:disable Salecto_DataIntegrity`
2. Run `php bin/magento setup:upgrade`
3. Run `php bin/magento module:enable Salecto_DataIntegrity`
4. Run `php bin/magento module:uninstall Salecto_DataIntegrity -c`
5. Run `php bin/magento setup:di:compile`
6. Run `php bin/magento setup:static-content:deploy da_DK en_US`
7. Run `php bin/magento cache:clean`

### Creating an integrity test
All new tests should be added to the IntegrityTest directory in the module.

Below is a sample test structure

- IntegrityTests (root directory for tests)
  - Classes (contains classes that can be used by all tests)
    - OverallCommonClass1.php
    - OverallCommonClass2.php
    - OverallCommonClass3.php
  - Test1 (contains all the files for test 1)
    - Classes (contains class that are only to be used by test 1)
      - Test1CommonClass1.php
      - Test1CommanClass2.php
    - Solutions (contains solutions of test 1)
      - Solution1.php
      - Solution1.md
      - Solution2.php
      - Solution2.md
    - Test.php
    - Test.md

Other than creating the files, new tests need to be updated in the di.xml file. 

- Look for a structure like below and add your test in there as a new item. This is so that the module can detect your test.

```
<type name="Salecto\DataIntegrity\Model\IntegrityTests">
      <arguments>
          <argument name="tests" xsi:type="array">
              <item name="product.without.website" xsi:type="object">Salecto\DataIntegrity\IntegrityTests\ProductWithoutWebsiteSingleStore\Test</item>
              <item name="product.with.url.path" xsi:type="object">Salecto\DataIntegrity\IntegrityTests\ProductWithUrlPath\Test</item>
              <item name="test1" xsi:type="object">Salecto\DataIntegrity\IntegrityTests\Test1\Test</item>
          </argument>
      </arguments>
</type>
```

- Also add a new structure like below that specifies the solutions for the test.

```
<type name="Salecto\DataIntegrity\IntegrityTests\Test1\Test">
      <arguments>
          <argument name="solutions" xsi:type="array">
              <item name="solution1" xsi:type="object">Salecto\DataIntegrity\IntegrityTests\Test1\Solutions\Solution1</item>
              <item name="solution2" xsi:type="object">Salecto\DataIntegrity\IntegrityTests\Test1\Solutions\Solution2</item>
          </argument>
      </arguments>
</type>
```

Naming Conventions:

- The test file should always be named Test.php
- Solution files should be named Solution1.php, Solution2.php...
- The readme files for test and solutions should have the same name as the test or solution.
