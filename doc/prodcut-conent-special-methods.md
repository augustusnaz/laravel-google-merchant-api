### `Product` special attribute functions

Any attribute (as allowed by the content class) can be assigned by simply calling it as function. E.g. to assign offerId, use

```php
$product->offerId(1);
```

The allowed attributes are defined in the `MOIREI\GoogleMerchantApi\Contents\Product\Product` class as per [this specification]( https://support.google.com/merchants/answer/7052112 ).



Additionally, special functions are defined for easily assigning certain attributes.

| Function         | Value Type                     | Description                                                  |
| ---------------- | ------------------------------ | ------------------------------------------------------------ |
| image     | `string`                 | Sets the `imageLink`. |
| lang                   | `string`                                          | Using the 2-letter designation, for example `"en"` or `"fr"`. Default: `"en"` |
| country | `string`                       | Sets the `targetCountry`. Default: `"AU"` |
| online                 | `boolean`                                         | Sets `channel` to `“online”`  or `"local"` |
| inStock                | `boolean`                                         | Sets `availability` to `“in stock”` or `“out of stock”`      |
| preorder               | none                                              | Sets `availability` to `“preorder”`              |
| availabilityDate       | `Carbon`/`string` |Takes a Carbon or string                                 |
| expirationDate | `Carbon`/`string`                    | Takes a Carbon or string                                     |
| category               | `string`                                          | Sets `googleProductCategory`                                 |
| price                  | `Closure`/`string`/`float`/`array` |Sets the `price`. If given an array, array keys must contain, `value` and `currency`. If given float or string, subsequent param (optional) should indicate currency|
| salePrice              | `Closure`/`string`/`float`/`array` | Sets the `salePrice`. If given an array, array keys must contain, `value` and `currency`. If given float or string, subsequent param (optional) should indicate currency |
| shipping               | `Closure`/`ProductShipping`/`array` |Appends to `shipping`|
| shippingHeight         | `Closure`/`Measure`/`double`/`array` | Sets the `shippingHeight`                                    |
| shippingLength         | `Closure`/`Measure`/`double`/`array` | Sets the `shippingLength`                                    |
| shippingWeight         | `Closure`/`Measure`/`double`/`array` | Sets the `shippingWeight`                                    |
| taxes                  | `Closure`/`Taxes`/`array` |Sets `taxes`|
| unitPricingBaseMeasure | `Closure`/`Measure`/`double`/`array` |Sets `unitPricingBaseMeasure`|
| unitPricingMeasure     | `Closure`/`Measure`/`double`/`array` |Sets `unitPricingMeasure`|
| salePriceEffectiveDate | `Carbon`/`string` |Sets `salePriceEffectiveDate`|
| sizes            | `array`/`any`                  | Sets the `sizes` if given an array. Appends otherwise |
| custom           | `string`/`array`, `mix`, `string` |Appends to `customAttributes`. If first param is not an array, subsequent params must indicate `value` and `type` (optional). If array, keys must contain `name`, `value` and `type` (optional). |
| customValues     | `array`                        | Calls the `custom` function per array element |