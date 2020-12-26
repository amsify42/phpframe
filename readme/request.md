## Request
`Request` is a helper class can be used to get input data from request.
<br/>
For example from action
```php
use PHPattern\Request;

class User extends Action
{
    public function create()
    {
        $params = Request::all();
    }
}
```
`Request::all()` will return all the bodyData/formData passed by `GET` `POST` or by other method.
<br/>
To get the specific value by key, you can do
```php
$name = Request::get('name');
```
If you want to specifically get `POST` data instead of body data, you can do
```php
$name = Request::post('name');
``` 
or for getting `GET` param that is passed in url
```
http://www.sage.com/api/user?name=amsify
```
```php
$name = Request::queryParam('name');
```
To get the direct input value from multi level array path of bodyData(if it is loaded as array)
```php
$machineId = Request::getPath('user.name');
```
`getPath` expects array keys to be passed separated by `.` and if bodyData array is in this format
```php
[
    'user' => [
        'name' => 'amsify'
    ]
]
```
It will return `amsify` or `NULL` if there is no key at the given path.