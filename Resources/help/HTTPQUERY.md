HttpQuery Bundle
====

Set set of helper to pass HTTP complex queries and convert them easily to query repositories

## Install

Install with composer.

`composer require adadgio/`

Make the following change to your `AppKernel.php` file to the registered bundles array.

```php
new Adadgio\HttpQueryBundle\AdadgioHttpQueryBundle(),
```

## Javascript usage

Add the following in your layout

```html
<script src="{{ asset('bundles/adadgiohttpquery/js/HttpQuery.js') }}"></script>
```

Use like this

```javascript
HttpQuery
    .setLimit(5)
    .setOffset(4)
    .addFilter('e.truc', 4)
    .addFilter('e.name($LIKE)', 'Romain')
;

var queryString = HttpQuery.getQuery();
var url = 'http://mywebsite.com/route' + queryString;

// and make an ajax call, for example
$.ajax({url: url, ...});
```

See below for example on how to handle this on the server side.

## Server side usage (PHP)

On the server side, you can just do.

```php
use Adadgio\\HttpQueryBundle\Http\Input;

public function indexController(Request $request)
{
    $httpInput = new Input($request);
    // print_r($httpInput);

    $whereConditions = $httpInput->getFilter(); array('name($LIKE) => ')

    $limit = $httpInput->getLimit(); // maybe... 5

    $offset = $httpInput->getOffset(); // probably... 0

    $orderBy = $httpInput->getSort(); // array('name' => 'DESC')
}
```

... or using annotation for even more style

```
@todo Not implemented yet
```
