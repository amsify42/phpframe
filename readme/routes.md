[< Main](https://github.com/amsify42/phpframe/blob/master/README.md)

## Routes
Routes can either be set automatically based on `Action` and its method combination or can be set manually in `/config/routes.php`.

#### Automatic
Automatic url is active by default in `app/Core/Boot.php`
```php
<?php

namespace App\Core;

use PHPattern\Router;

class Boot
{
    /**
     * Decides whether auto route is allowed or not.
     * @var boolean
     */
    protected $autoRoute = true;
}   
```
When you create a action and its method, it will work for the route `/user`
```php
<?php

namespace App\Actions;

use PHPattern\Action;

class User extends Action
{
    public function index()
    {
        /**
         * Your logic
         */
    }
}
```
and this method will work for `/user/details` route.
```php
public function details()
{
    /**
     * Your logic
     */
}
```
<br/>
Automatic routing also identifies the numeric values passed in the url pattern and pass them as parameters to action method.

```php
public function byId($id)
{
    /**
     * Your logic
     */
}
```
Above method will work for route `/user/by-id/42` or `/user/42/by-id`.
<br/>
`42` is the dynamic value you can pass and notice that methods with camelCase names will be converted to dashed version as `by-id` will refer to `byId()` method.

**Note**: There is one issue with automatic routing, it will accept all method type `GET` `POST` or other.

#### Manual
This way of setting route will give you the options to set restrictions on route pattern, its method type and url parameters. You can set routes in `/config/routes.php` like this

#### Callback - Action
We can set closure function to route action.
```php
Route::get('/', function(){
    return response()->json('Welcome', true);
});
```
It will work for base path `/` with `GET` method. This method takes two paramters.
```
1. url pattern
2. Closure function [or] Action name [or] Action/Method combination separated by @
```
#### Action
```php
Route::get('/', Actions\Home::class);
```
If only class name is passed in 2nd parameter, it will consider and call **index** method of that class.
```php
<?php

namespace App\Actions;

use PHPattern\Action;

class User extends Action
{
    public function index()
    {
        /**
         * route action will reach here
         */
    }
}
```
#### Action@Method
For different method type like `POST`, you can do this
```php
Route::post('/user/create', Actions\User::class@create);
```
The above route will only work for `POST` method and will call `create()` method in `app\Actions\User` action. For dynamic segment in url pattern, you can set it like this
```php
Route::get('/user/{userId}/details', Actions\User::class@details);
```
It will work for this action/method with one parameter.
```php
class User extends Action
{
    public function details($userId)
    {
        /**
         * Your logic
         */
    }
}
```
Same dynamic segment value in uri pattern works for closure function as well.
```php
Route::get('/user/{userId}/details', function($userId){
    return response()->json('User details', true, ['userId' => $userId]);   
});
```
Unlike automatic routing, you can set route pattern to any length with any number of dynamic segments.
<br/>
##### Group
For grouping routes with similar prefix pattern, you can use this method
```php
Router::group('user', function(){
    Route::get('address', Actions\User::class@address);
    Route::post('update', Actions\User::class@update);
});
```
[or]
```php
Router::group(['prefix' => 'user', 'class' => Actions\User::class], function(){
    Route::get('address', @address);
    Route::post('update', @update);
});
```
The above grouping will work for routes `GET` - `/user/address` and `POST` - `/user/update`.
<br/><br/>
For setting the unique name of the route, you can call this method as a chain
```php
Route::post('/user/create', Actions\User::class@create)->name('user_create');
```
Setting the name of the route will help in generating url. You can check [GENERATING URLs](#generating-urls) below for the details.
###### Group Options
These are the options can be passed as array for grouping routes.
1. prefix
2. class
3. middleware
4. include
```php
Router::group(['prefix' => 'user', 'class' => Actions\User::class, 'middleware' => Middlewares\UserAuth::class, 'include' => 'global/user_functions'], {
    Route::get('address', @address);
    Route::post('update', @update);
});
```
Option `prefix` is the prefix segment of uri for grouped routes.
<br/><br/>
Option `class` will be the target action for grouped routes.
<br/><br/>
`middleware` is for filtering request for grouped routes. We can also pass multiple middleware classes as array.
```php
Router::group(['prefix' => 'user', 'middleware' => [Middlewares\UserAuth::class, Middlewares\UserType::class]], function(){
    /**
     * Your routes
     */
});
```
`middleware` can also be set for specific route.
```php
Route::get('user/details', Actions\User::class@details)->middleware(Middlewares\UserAuth::class);
```
`include` option expects path of non class based php files to load only for grouped routes. The path of the file must be from base path `App/`. We can also pass multiple file paths.
```php
Router::group(['prefix' => 'user', 'include' => ['global/user_functions', 'global/auth_functions']], function(){
    /**
     * Your routes
     */
});
```
include can also be set for specific route.
```php
Route::get('user/details', Actions\User:class@details)->include('global/user_functions');
```

**Note:** Make sure you set
```php
protected $autoRoute = false;
```
in `app/Core/Boot.php` if you do not want to allow automatic routes.

#### Generating URLs
With `PHPattern\Router\Route` class we can also generate urls. Example if this is our current/active route
```
/user/info
```
We can call this method to get the current absolute url for it.
```php
echo Route::current();
```
It will print `http://www.site.com/user/info` if `http://www.site.com/` is the base url.
<br/>
<br/>
And we can pass url parameters to it like this
```php
echo Route::current(['name' => 'amsify', 'id' => 123]);
```
It will print `http://www.site.com/user/info?name=amsify&id=123` if `http://www.site.com/` is the base url.
<br/>
<br/>
If route pattern have variable terms in it.
```
/user/{userId}/details
```
and we have set the name for this route in `/config/route.php` like this
```php
Route::get('user/{userId}/details', Actions\User::class@details)->name('user_details');
```
We can call this method to get the dynamic url for it.
```php
echo Route::url('user_details', ['userId' => 123]);
```
It will print `http://www.site.com/user/123/details` if `http://www.site.com/` is the base url.
<br/>
If we pass extra parameter to this function
```php
echo Route::url('user_details', ['userId' => 123, 'id' => 2, 'name' => 'amsify']);
```
it will be appended to the url in the end as query parameters
```
http://www.site.com/user/123/details?id=2&name=amsify
```

#### URI Segment
We can also put restriction on what type of value should be passed through dynamic URI segment.
```php
Route::get('user/{userId}/details', Actions\User::class@details)->segment('userId', 'number');
```
The above setting will allow only numbers for `userId` segment in route uri. For multiple, you can pass like this
```php
Route::get('user/{userId}/details/{userName}', Actions\User::class@details)->segments(['userId' => 'number', 'userName' => '/[A-Za-z]+/']));
```
If you notice the value we are passing for `userName` is a regular expression. The URI segment have only two options, `number` or any regular expression.
<br/><br/>
For setting validation globally for all routes having same uri segment name.
```php
\App\Core\Router::URISegmentsType('id', 'number');
```
`id` is the uri pattern name and `number` is validation. For setting same validation for multiple uri segment names.
```php
\App\Core\Router::URISegmentsType(['id', 'userId', 'placeId'], 'number');
```
**Note:** You have to do the above setting in protected method `_preload` of [`/Core/Boot.php`](https://github.com/amsify42/phpframe/blob/master/app/Core/Boot.php) class.
### 3. Action
Action can be created manually in `/Actions` directory or go to base path of sage repository and run this command from console
```
php pcli create action Sample 
```
This will create the action at `/Actions/Sample.php` with code.
```php
<?php

namespace App\Actions;

use PHPattern\Action;

class Sample extends Action
{
    
}
```
You can keep adding methods to the action based on requirement or route pattern.
