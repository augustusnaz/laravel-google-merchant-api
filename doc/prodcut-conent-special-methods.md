### `Product` special attribute functions

Any attribute (as allowed by the content class) can be assigned by simply calling it as function. I.e. to assign offerId, use

```php
$product->offerId(1);
```

Additionally, special functions are defined for easily assigning certain attributes.

| Function         | Value Type                     | Description                                                  |
| ---------------- | ------------------------------ | ------------------------------------------------------------ |
| image     | `string`                 | Sets the `imageLink`. |
| lang                   | `string`                                          | Using the 2-letter designation, for example `"en"` or `"fr"`. Default: `"en"` |
| country | `string`                       | Sets the `targetCountry`. Default: `"AU"` |
| online                 | `boolean`                                         | Sets the channel attribute to `“online”`  or `"local"` |
| inStock                | `boolean`                                         | Sets `availability` to `“in stock”` or `“out of stock”`      |
| preorder               | none                                              | Sets `availability` to `“preorder”`              |
| availabilityDate       | `Carbon`|`string`                                 | Sets the `availabilityDate` in the [ISO 8601](http://en.wikipedia.org/wiki/ISO_8601) format. |
| expirationDate | `Carbon`|`string`      | Sets the `expirationDate` in the [ISO 8601](http://en.wikipedia.org/wiki/ISO_8601) format. |
| category               | `string`                                          | Sets `googleProductCategory`                                 |
| price                  | `string`|`array`| `callback`| `numeric`, `string` | Sets the `price`. Takes the price `value` and `currency` (default is AUD) in string. |
| salePrice              | `string`|`array`| `callback`| `numeric`, `string` | Sets the `salePrice`. Takes the price `value` and `currency` (default is AUD) in string. |
| shipping               | `ProductShipping`|`array`| `callback`             | Sets the `shipping`.                                         |
| shippingHeight         | `Measure`|`array`| `callback` | `double`          | Sets the `shippingHeight`.                                   |
| shippingLength         | `Measure`|`array`| `callback` | `double`          | Sets the `shippingLength`.                                   |
| shippingWeight         | `Measure`|`array`| `callback` | `double`          | Sets the `shippingWeight`. |
| taxes                  | `Taxes`|`array`|`callback`                        | Sets the `taxes`. |
| unitPricingBaseMeasure | `Measure`|`array`| `callback`|`long`              | Sets the `unitPricingBaseMeasure`.                           |
| unitPricingMeasure     | `Measure`|`array`| `callback`|`double`            | Sets the `unitPricingMeasure`. |
| salePriceEffectiveDate | `Carbon`|`string`, `Carbon`|`string`              | Sets the `salePriceEffectiveDate`. Sets time effective *until* and time effective *from* (optional, defaults to `now`) |
| sizes            | `array`                        | Sets the `sizes` |
| custom           | `array`|`string`, `mix`, `string` |Appends to the `customAttributes`. Custom values can be passed as array with keys `name`, `value` and `type`. If array is not passed then the custom attribute's value must follow. The `type` can also be passed, or left to auto resolve. |
| customValues     | `array`                        | Calls the `custom` function per array element |