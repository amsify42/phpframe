[< Main](https://github.com/amsify42/phpframe/blob/master/README.md)

## Response
`Response` is a simple helper which can be used to print/send response data.
<br/>
For example from action
```php
class User extends Action
{
    public function index()
    {
        /**
         * Do your code
         */
        $response = new \PHPattern\Response();
        return $response->json('Success', true);
    }
}
```
We can also do it by calling a helper method
```php
public function index()
{
    /**
     * Do your code
     */
    return response()->json('Success', true);
}
```
`response()` will create the new instance of `\PHPattern\Response` and return it.
<br/>
Generally any response type method from this instance expects 2 parameters. First takes message as `string` and second boolean. The above response will send json response:
```json
{
    "status": true,
    "message": "Success"
}
```
For sending more data, you can pass it in 3rd parameter.
```php
return response()->json('Success', true, ['created_id' => 123]);
```
and its json format will be
```json
{
    "status": true,
    "message": "Success",
    "data": {
        "created_id" : 123
    }
}
```
Check [`/PHPattern/Response.php`](https://github.com/amsify42/phpattern/blob/master/src/Response.php) for more parameters and response types info.
<br/>
Validation errors response can also be sent independantly.
<br/>
Example from action
```php
class User extends Action
{
    public function details($userId)
    {
        if($userId <= 0)
        {
            return response()->validationErrors(['Please pass valid userId']);
        }
        /**
         * do your code
         */
    }
}
```
This method only expects one parameter of type `array`, you can pass multi level array with key information in it.
<br/>
Example:
```php
return response()->validationErrors(['place_info' => 'sage_place_id or place_id is required']);
```
