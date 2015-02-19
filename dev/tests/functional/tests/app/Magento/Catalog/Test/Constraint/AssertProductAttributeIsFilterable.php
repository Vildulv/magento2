<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Check whether the attribute filter is displayed on the frontend in Layered navigation.
 */
class AssertProductAttributeIsFilterable extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Check whether the attribute filter is displayed on the frontend in Layered navigation.
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param InjectableFixture $product
     * @param CatalogProductAttribute $attribute
     * @param CmsIndex $cmsIndex
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        InjectableFixture $product,
        CatalogProductAttribute $attribute,
        CmsIndex $cmsIndex,
        FixtureFactory $fixtureFactory
    ) {
        $fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => 'product_with_category_with_anchor',
                'data' => [
                    'category_ids' => [
                        'presets' => null,
                        'category' => $product->getDataFieldConfig('category_ids')['source']->getCategories()[0]
                    ]
                ],
            ]
        )->persist();

        $cmsIndex->open()->getTopmenu()->selectCategoryByName($product->getCategoryIds()[0]);
        $label = $attribute->hasData('manage_frontend_label')
            ? $attribute->getManageFrontendLabel()
            : $attribute->getFrontendLabel();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array($label, $catalogCategoryView->getLayeredNavigationBlock()->getFilters()),
            'Attribute is absent in layered navigation on category page.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is present in layered navigation on category page.';
    }
}
