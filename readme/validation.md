## Validation
Request also comes with validation filter. But first we need to create/generate request class for putting validation rules.
<br/>
To generate request class, we can run this command from root
```
php pcli create request Sample
```
This will create file at `app/Request/Sample.php` with code content
```php
<?php

namespace App\Request;

use PHPattern\Request\Form;

class Sample extends Form
{
    protected function rules()
    {
        return [
            
        ];
    }

}
```
Under `rules()` method we can set rules in `array` and return. Example:
```php
protected function rules()
{
    return [
        'name'  => 'required|string',
        'id'    => 'required|int',
        'tags'  => 'requiredif|array'
    ];
}
```
The above 3 validation rules are for inputs coming from request. The key values can be separated by `|` symbol for multiple rules.
<br/>
After validation is being successfully passed, you can get it like this in action method.
```php
namespace App\Actions;

use PHPattern\Action;
use App\Request\Sample;

class User extends Action
{
    public function details($userId, Sample $request)
    {
        $params = $request->all();
    }
}
```
The `$request` instance here can call all the methods that [`\PHPattern\Request`](https://github.com/amsify42/phpattern/blob/master/src/Request.php) can call.
```php
/**
 * For getting all bodyData/formData
 */
$request->all();
/**
 * For getting all params from url
 */
$request->queryParams();
/**
 * For getting data from body/post/get by key
 */
$request->get('name');
/**
 * For getting data from post by key
 */
$request->post('name');
/**
 * For getting param from url
 */
$request->queryParam('name');
/**
 * To get the direct input value from multi level array path of bodyData(if it is loaded as array)
 */
$request->getPath('user.name'); /* It will either return value if key exit [or] will return NULL */
```
These are the rules, you can set/use in validation rules:
```
required - Check value is set and not empty
requiredif - It just let the other rules check the input if value is passed
string - It checks whether input is of type string
int - It checks whether input is of type int
float - It checks whether input is of type float
bool - It checks whether input is of type bool
array - It checks whether input is of type array
```
#### Keys - Rule
```php
protected function rules()
{
    return [
        'tags' => 'required|keys:id,name'
    ];
}
```
The above setting will check whether `tags` input is set, it is array and having `id` and `name` keys with non empty value.
#### Child Keys - Rule
```php
protected function rules()
{
    return [
        'tags' => 'childkeys|keys:id,name'
    ];
}
```
The above setting will check whether `tags` input is set, it is array and its all child elements have `id` and `name` keys with non empty value.
#### Custom - Rule
We can also add public function to form request class for custom validation.
```php
<?php

namespace App\Request;

use PHPattern\Request\Form;

class Sample extends Form
{
    protected function rules()
    {
        return [
            'name' => 'required|customCheck'
        ];
    }

    public function customCheck()
    {
        if($this->value() == 'something')
        {
            return false;
        }
        return true;
    }
}
```
Notice public method `customCheck` is created and added its name to the validation rule of `name` input. The method should return boolean `true` if it is validated as per your logic.
<br/><br/>
For custom validation error message you can simply return message of type `string` instead of returning boolean.
```php
public function customCheck()
{
    if($this->value() == 'something')
    {
        return 'Something is not allowed';
    }
    return true;
}
```
These are the methods we can use from within request class.
```php
public function customCheck()
{
    /**
     * To get the value of input value applicable for custom rule
     */
    $this->value();
    /**
     * To trim and get the value of input value applicable for custom rule
     */
    $this->value('trim');
    /**
     * To get the key value of the input if it is array
     */
    $this->valKey('name');
    
}
```
and we can call all the request methods with `$this` instance
```php
public function customCheck()
{
    /**
     * For getting all bodyData/formData
     */
    $this->all();
    /**
     * For getting all params from url
     */
    $this->queryParams();
    /**
     * For getting data from body/post/get by key
     */
    $this->get('name');
    /**
     * For getting data from post by key
     */
    $this->post('name');
    /**
     * For getting param from url
     */
    $this->queryParam('name');
    /**
     * To get the direct input value from multi level array path of bodyData(if it is loaded as array)
     */
    $this->getPath('user.name'); /* It will either return value if key exit [or] will return NULL */
}
```
#### Validation in Action
If you want to do validation without creating any request class, you can also do it like this and use the rules.
```php
namespace App\Actions;

use PHPattern\Action;
use PHPattern\Request\Validation;

class User extends Action
{
    public function index()
    {
        $validation = new Validation();
        $rules      = [
                        'id'    => 'required|int',
                        'name'  => 'required|string'
                    ];
        if(!$validation->validated($rules))
        {
            return $validation->responseErrors();
        }
        /**
         * else do your logic
         */
    }
}
```
**Note:** Custom validation method will not be applicable here.
#### TypeStruct
This is the more readable way of setting validation for request data. For setting you need to generate/create typestruct file
```
php pcli create typeStruct Sample
```
File will be created at `app/TypeStruct/Sample.php` and generated content will be
```php
<?php

namespace App\TypeStruct;

export typestruct Sample {
    
}
```
**Note:** This file does not contain php syntax, its just a custom script which will be interpreted in application during validation.
```php
<?php

namespace App\TypeStruct;

export typestruct Sample {
    id: int,
    name: string
}
```
The above script which looks like javascript/json object can be created for setting validation rules. The rules can also be set for multi level data structure.
```php
<?php

namespace App\TypeStruct;

export typestruct Sample {
    id: int,
    name: string,
    info : {
        is_verified: boolean,
        price: float,
        items: array
    }
}
```
The above sample code works for data passed through request.
```php
[
    'id' => 1,
    'name' => 'Sample',
    'info' => [
        'is_verified' => true,
        'price' => 40.20,
        'items' => [1,2,3]
    ]
]
```
After setting the rules, you need to assign the full class name to protected property `$typeStruct` in request class.
```php
<?php

namespace App\Request;

use App\Core\Request\Form;

class Sample extends Form
{
    protected $typeStruct = \App\TypeStruct\Sample::class;
}
```
For adding custom methods to typestruct validation, you can add methods to request class
```php
<?php

namespace App\Request;

use App\Core\Request\Form;

class Sample extends Form
{
    protected $typeStruct = \App\TypeStruct\Sample::class;

    public function customCheck()
    {
        if($this->value() == 'something')
        {
            return false;
        }
        return true;
    }
}
```
and add the method name to the applicable element in typestruct.
```php
<?php

namespace App\TypeStruct;

export typestruct Sample {
    id: int,
    name: string<customCheck>,
    info : {
        is_verified: boolean,
        price: float,
        items: array
    }
}
```
**Note:** Both setting rules in `typStruct` and `rules()` method will not work together. If both are set, then `typStruct` will be executed as priority.
<br/><br/>
This is a multi level sample typestruct file, which will give an idea of how we can set validation with more external typestruct file as a child element.
```php
<?php

namespace App\TypeStruct;

use App\TypeStruct\Category;

export typestruct Sample {
    id: int,
    name: string,
    items: int[2],
    address: {
        city: string,
        pincode: int,
        details : {
            state : string,
            country: string,
            more : {
                code: int,
                continent: string
            }
        }
    },
    categories : Category[]
}
```
```php
<?php

namespace App\TypeStruct;

export typestruct Category {
    id: int,
    name: string,
    price: float,
    details : {
        order : int,
        parent: int
    }
}
```
If you have noticed that `App\TypeStruct\Category` is a child element of `categories` which is array in `App\TypeStruct\Sample` typestruct file and will be validated at all the levels.
<br/><br/>
These are the common types we can use for validation in typestruct file
```
string
int
float
boolean
any
```
and for arrays
```
array
[]
string[]
int[]
float[]
boolean[]
```