[< Main](https://github.com/amsify42/phpframe/blob/master/README.md)

## Middleware
Middleware can be created manually in `app/Middlewares` directory or go to root path of sage repository and run this command from console
```
php pcli create middleware Sample 
```
This will create the middleware at `app/Middlewares/Sample.php` with code.
```php
<?php

namespace App\Middlewares;

use PHPattern\Middleware;
use PHPattern\Request\Data;

class Sample extends Middleware
{
    public function process(Data $requestData)
    {
        /**
         * Do your logic
         */
        return false;
    }

}   
```
The `process` method takes one parameter of type [`PHPattern\Request\Data`](https://github.com/amsify42/phpattern/blob/master/src/Request/Data.php) which forward the request data to this method. You can write logic in it, set response code, message and return `true` or `false` based on your logic.
<br/>
<br/>
**Why we create middleware?**
<br/>
This is nothing but a filter to verify whether the request needs to be processed further for execution else restrict based on given conditions. You can create multiple middlewares and add it to `$middlewares` protected property list in [`app/Core/Boot.php`](https://github.com/amsify42/phpframe/blob/master/app/Core/Boot.php) file.
