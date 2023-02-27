<img src="https://user-images.githubusercontent.com/7572058/221666178-3157c1b8-fd83-48e8-956c-ec63dbcbead5.jpeg"/>

# Data Migrator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/oguzhankrcb/datamigrator.svg?style=flat-square)](https://packagist.org/packages/oguzhankrcb/datamigrator)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/oguzhankrcb/datamigrator/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/oguzhankrcb/datamigrator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/oguzhankrcb/datamigrator/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/oguzhankrcb/datamigrator/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/oguzhankrcb/datamigrator.svg?style=flat-square)](https://packagist.org/packages/oguzhankrcb/datamigrator)

Data Migrator is a PHP/Laravel package that helps you migrate data from one model to another, even if they have
different
structures.
It's especially useful when you're migrating data between models with different database schemas.

## Installation

You can install the package via composer:

```bash
composer require oguzhankrcb/datamigrator
```

## Usage

### Transforming Data

To transform data from one model to another, use the `transformData` method. This method takes two arrays:
`$toModelPrototype` and `$fromModel`.

`$toModelPrototype` should be an array that describes the structure of the new model, with the keys being the names of
the
new fields, and the values being the names of the fields from the old model that the new fields should be based on. For
example:

```php
$toModelPrototype = [
    'id'         => '[id]',
    'unique_id'  => '[unique_number.id]',
    'name'       => '[data->name]',
    'categories' => [
        'first_category'  => '[data->categories->category_2]',
        'second_category' => '[data->categories->category_3]',
    ],
    'alias_with_item_code' => '[data->alias][data->item->code]',
    'alias'                => '[data->alias]',
    'item_code'            => '[data->item->code]',
    'status'               => '[data->status]',
];
```

`$fromModel` should be an array that represents a single row of data from the old model, with the keys being the names
of the fields from the old model, and the values being the actual values.
For example:

```php
$fromModel = [
    'id'            => 1,
    'unique_number' => 'lxAxmUlkfc',
    'data'          => [
        'name'       => 'John Doe',
        'alias'      => 'JD',
        'categories' => [
            'category_1' => 'Bronze',
            'category_2' => 'Silver',
            'category_3' => 'Gold',
        ],
        'item' => [
            'code' => 196854,
        ],
        'status' => true,
    ],
];
```

Here's an example of how to use `transformData`:

```php
use Oguzhankrcb\DataMigrator\Facades\DataMigrator;

$newData = DataMigrator::transformData($toModelPrototype, $fromModel);
```

The `$newData` array will contain the transformed data, with the keys being the names of the new fields, and the values
being the corresponding values from the old model.

Output Example:

```php
[
    'id'         => 1,
    'unique_id'  => 'lxAxmUlkfc1',
    'name'       => 'John Doe',
    'categories' => [
        'first_category'  => 'Silver',
        'second_category' => 'Gold',
    ],
    'alias_with_item_code' => 'JD196854',
    'alias'                => 'JD',
    'item_code'            => '196854',
    'status'               => true,
]
```

### Transferring Data

To transfer all data from one model to another, use the `transferAllDataFromModelToModel` method. This method takes
three
arguments: `$transferToModel`, `$toModelPrototype`, and `$transferFromModel`.

`$transferToModel` should be the fully qualified class name of the model you want to transfer the data to. For example:

```php
$transferToModel = \App\Models\User::class;
```

`$toModelPrototype` should be the same array you used with `transformData`.

`$transferFromModel` should be the fully qualified class name of the model you want to transfer the data from. For
example:

```php
$transferFromModel = \App\Models\LegacyUser::class;
```

Here's an example of how to use `transferAllDataFromModelToModel`:

```php
use App\Models\Order;
use App\Models\Invoice;
use Oguzhankrcb\DataMigrator\Facades\DataMigrator;

// Define the fields to transfer from Order to Invoice
$toModelPrototype = [
    'invoice_number' => '[order_number]',
    'customer_name' => '[customer->name]',
    'customer_email' => '[customer->email]',
    'total_amount' => '[amount]',
    'total_amount_with_currency' => '[amount]€',
];

// Transfer the data from Order to Invoice
DataMigrator::transferAllDataFromModelToModel(Invoice::class, $toModelPrototype, Order::class);
```

In this example, we define the fields we want to transfer from the `Order` model to the `Invoice` model using the
`$toModelPrototype` array. Then we call the
`transferAllDataFromModelToModel` method, passing in the `Invoice` and `Order` models and the `$toModelPrototype` array.

This method will transfer all the data from the `Order` model to the `Invoice` model, creating a new `Invoice` model for
each
`Order` model in the database.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! If you find any bugs or issues,
please [open a new issue](https://github.com/oguzhankrcb/DataMigrator/issues/new) or submit a pull request.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Oğuzhan KARACABAY](https://github.com/oguzhankrcb)
- [All Contributors](../../contributors)

## License

The DataMigrator package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
