# Laravel Man Pagination

[![Latest Stable Version](https://poser.pugx.org/dev-and-web/laravel-man-pagination/v/stable)](https://packagist.org/packages/dev-and-web/laravel-man-pagination)
[![License](https://poser.pugx.org/dev-and-web/laravel-man-pagination/license)](https://packagist.org/packages/dev-and-web/laravel-man-pagination)

Laravel Man Pagination is a Open Source PHP library of a simple manual pagination.
With paging, you have no limit in a Laravel project for paging.
You can even use this pagination with "DB::select" queries.

*Paginate easily without limit!*
```php
<?php

$pagination = new Pagination();

$pagination->paginate($countElements);

$limit = $pagination->getLimit();
$offset = $pagination->getOffset();

// Here your listing of elements with a loop

{!! $pagination->render() !!}
{!! $pagination->perPage() !!}
```




### Requirements

* PHP >= 7.4





## * Summary *

* Introduction
* Installation
* Pagination instance methods
* Examples
* Custom config
* Support





## Introduction

This Open Source pagination contains PHP files, and one CSS style sheet.
The CSS style sheet is in "/vendor/dev-and-web/laravel-man-pagination/src/css/" directory.
You can edit them according to your needs.

This pagination also allows you to generate a "per page".
This will generate a form HTML tag with a select HTML tag and clickable options.





## Installation

Installation via Composer:
```
composer require dev-and-web/laravel-man-pagination
```





## Methods

| Return type | Name | Description |
| ------- | -------------- | ----------- |
| void | __construct(array $options = []) | Constructeur |
| void | paginate(int $count) | (to use in the Controller) Activate the pagination |
| int | getLimit() | (to use in the Controller) LIMIT: Number of items to retrieve |
| int | getOffset() | (to use in the Controller) OFFSET: From where you start the LIMIT |
| string | render() | (to use in the view) Make the rendering of the pagination in HTML format |
| string | perPage() | (to use in the view) Make the rendering of the per page in HTML format |
| int | getCount() | (to use wherever you want) Number of items on which we make the pagination |
| int | getCountOnCurrentPage() | (to use wherever you want) Number of items on the current page |
| int | getFrom() | (to use wherever you want) To return the indexing of the first item to the current page |
| int | getTo() | (to use wherever you want) To return the indexing of the last item to the current page |
| int | getCurrentPage() | (to use wherever you want) Current page |
| int | getNbPages() | (to use wherever you want) Number of pages |
| bool | hasMorePages() | (to use wherever you want) True if there are pages after that current page |
| bool | isFirstPage() | (to use wherever you want) True if the current page is the first page |
| bool | isLastPage() | (to use wherever you want) True if the current page is the last page |





## Examples

### Example with "DB::select"

#### Controller:
```php
<?php

use LaravelManPagination\Pagination;

class CustomerController extends Controller
{
    public function index()
    {
        $sqlForCount = DB::select('
            SELECT COUNT(id) AS nb_customers
            FROM '.Database::CUSTOMERS.'
        ');

        $count = $sqlForCount[0]->nb_customers;

        $pagination = new Pagination();

        $pagination->paginate($count);
        
        $limit = $pagination->getLimit();
        $offset = $pagination->getOffset();

        $customers = DB::select('
            SELECT *
            FROM '.Database::CUSTOMERS.'
            ORDER BY id DESC
            LIMIT '.$limit.' OFFSET '.$offset.'
        ');

        return view('customer.index', [
            'customers' => $customers,
            'paginator' => $pagination,
        ]);
    }
}
```

#### View:
```html
<div style="text-align: center;">
    @foreach ($customers as $customer)
        {{ $customer->id }}
        <br>
    @endforeach
</div>
            
<div style="text-align: center;">
    {{-- show the pagination --}}
    {!! $pagination->render() !!}

    {{-- show the per page --}}
    {!! $pagination->perPage() !!}
</div>

<div style="text-align: center; margin-top: 20px;">
    Number total of items: {!! $pagination->getCount() !!}
    <br>
    Number of items on the current page: {!! $pagination->getCountOnCurrentPage() !!}
    <br>
    Indexing of the first and last items to the current page: From: {!! $pagination->getFrom() !!} - To: {!! $pagination->getTo() !!}
    <br>
    Current page: {!! $pagination->getCurrentPage() !!}
    <br>
    Number of pages: {!! $pagination->getNbPages() !!}
    <br>
    True if there are pages after that current page: {!! var_dump($pagination->hasMorePages()) !!}
    <br>
    True if the current page is the first page: {!! var_dump($pagination->isFirstPage()) !!}
    <br>
    True if the current page is the last page: {!! var_dump($pagination->isLastPage()) !!}
</div>
```


### Example of Controller with Eloquent

```php
<?php

use LaravelManPagination\Pagination;

class CustomerController extends Controller
{
    public function index()
    {
        $count =  Customer::count('id');

        $pagination = new Pagination();

        $pagination->paginate($count);
        
        $limit = $pagination->getLimit();
        $offset = $pagination->getOffset();

        $customers = Customer::skip($offset)->take($limit)->orderBy('id', 'DESC')->get();

        return view('customer.index', [
            'customers' => $customers,
            'paginator' => $pagination,
        ]);
    }
}
```





## Add argument(s) to the instance

```php
<?php

// Number of Elements per page
$pagination = new Pagination(['pp'=>50]);
// Is 15 by default

// Number of links alongside the current page
$pagination = new Pagination(['number_links'=>5]);
// Is 10 by default

// The choice to select potentially generate with perPage()
$pagination = new Pagination(['options_select'=>[5, 10, 50, 100, 500, 'all']]);
// The value of 'options_select' must be a array.
// Only integers and 'all' is permitted.
// Options are [15, 30, 50, 100, 200, 300] by default

// To change the CSS style of the pagination (to another CSS class as default)
$pagination = new Pagination(['css_class_p'=>'name-css-class-of-pagintion']);
// The CSS class name is by default "pagination"

// To change the CSS style of a per page (select) (to another id id as default)
$pagination = new Pagination(['css_id_pp'=>'name-css-id-of-per-page']);
// The CSS ID name is by default  "per-page"
```





## Custom config

You can customize the default config by creating this file in your laravel project: /config/man-pagination.php

### Example of custom config

```php
<?php

return [

    /**
     * For pagination
     */
    'text_next'     => 'Suivant',
    'text_previous' => 'Précédent',

    /**
     * For per page
     */
    'text_per_page' => 'Par page',
    'text_all' => 'Tous',

];
```





## Support

Bugs and security Vulnerabilities
If you discover a bug or a security vulnerability, please send a message to Stephen. Thank you. All bugs and all security vulnerabilities will be promptly addressed.
