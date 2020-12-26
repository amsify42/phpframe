# Model
The [Model](https://github.com/amsify42/phpattern/blob/master/src/Database/Model.php) class helps in creating db models for different database table and easily perform **insert**/**update**/**delete** queries.

## Table of Contents
1. [Class](#1-class)
2. [Class Properties](#2-class-properties)
3. [Querying](#3-querying)
4. [Relations](#4-relations)

### 1. Class
You can also create/generate class with extends `PHPattern\Database\Model` class.
<br/>
To generate the model class file, you can use this command from root of sage repo.
```
php pcli create model User
```
This will create file at `app/Models/User.php` with content
```php
<?php

namespace App\Models;

use PHPattern\Database\Model;

class Users extends Model
{
    
}
```
The name of the class will be converted to table name `users`.
<br/>
If your database table name is different than model class name then you can add table name in protected property of your model class
```php
class Users extends Model
{
    protected $table = 'app_users';    
}
```
or do it while generating file itself
```
php pcli create model Users -table app_users
```
[or]
```
php pcli create model Users -table=app_users
```
Now, all methods called using this model will fetch data from table `app_users` instead of `users`
<br/><br/>
If you have table name with underscore between name and you do not want to define protected property **$table**, then you can also name model class in such a way that it will convert it to table name

```php
class AppUsers extends Model
{

}   
```
```php
class MobileAPP extends Model
{

}
```             
As you can see above, there are two example which use camel case and specially second one which has second section of class name having all capital letters.
<br/>
Here `AppUsers` will work for table name `app_users`
<br/>
and `MobileAPP` will work for table name `mobile_app`

### 2. Class Properties
These are the properties you can inherit from base model class and set it accordingly.
#### Table name
```php
class Users extends Model
{
    protected $table = 'users'; 
}
```

#### Primary Key
```php
class Users extends Model
{
    protected $primaryKey = 'id';
}
```
This will work as a primary key when getting/updating/deleting single record.

#### Time Stamps
```php
class Users extends Model
{
    protected $timestamps = true;
}
```
This is for storing timestamps in columns like `created_at` & `updated_at` automatically while inserting/updating table.

#### More CLI params
While generating model class from cli, you can also pass these parameters.
```
table
primaryKey
timestamps
```
Example:
```
php pcli create model Sample -table=samples
php pcli create model Sample -primaryKey=id
php pcli create model Sample -timestamps=true
```
or we can use combination of all these parameters.

### 3. Querying
#### Selection
```php
$users = User::all();
```
It will get all the rows from table. For selecting specific columns, we can pass it in first parameter.
```php
$users = User::select(['id', 'name', 'created_at', 'updated'])->all();
```
#### Paginate
For getting paginated rows from table.
```php
$users = User::paginate(10);
```
For selecting specific columns.
```php
$users = User::select(['id', 'name', 'created_at', 'updated'])->paginate(10);
```
For getting based on conditions, we can do
```php
$users = User::select(['id', 'name'])->where('is_active', 1)->paginate(10);
```
This paginate method automatically takes page number from key `page` from either formdata or body data or from url query parameter based on request type POST, GET or other. We can also pass page number in second parameter.
```php
$users = User::select(['id', 'name'])->where('is_active', 1)->paginate(10, 1);
```
**Note:** By default it will return the array of model class objects, each object containing row data.

#### First
For getting the first row from table
```php
$user = User::select(['id'])->where('is_active', 1)->first();
```
For getting first row by primaryKey id. Method **first()** takes optional parameter id.
```php
$user = User::select(['id'])->first(1);
```
It will either return model class object with row data or `NULL` value if row not found.

#### OrderBy
For ordering by rows by columns
```php
$users = User::select(['id', 'name'])->where('is_active', 1)->orderBy('id', 'DESC')->all();
```
#### GroupBy
For getting rows group by
```php
$users = User::select(['id', 'name'])->where('is_active', 1)->groupBy('id')->all();
```
#### Having
For getting rows based on alias conditions
```php
$users = User::select('COUNT(1) AS count')->where('is_active', 1)->having(['count', '>', 10])->all();
```
#### Limit
For getting rows based on limits
```php
$users = User::select(['id', 'name'])->limit(10);
```
limit method also takes 2nd param as **OFFSET**
```php
$users = User::select(['id', 'name'])->limit(10, 10);
```

#### Insert
We can insert by passing row data as array
```php
$row = [
    'name'  => 'amsify',
    'status'=> 'active'
];
$userId = User::insert($row);
```
It will return newly created row id. We can create new instance of object model like this
```php
$user         = new User();
$user->name   = 'amsify';
$user->status = 'active';
$user->save();
```
It will return `true` if inserted and `$user->id` will have its newly inserted id.
#### Update
```php
$userId = 10;
$data   = [
    'name'  => 'amsify',
    'status'=> 'active'
];
$noOfRows = User::update($data, $userId);
```
This method 2nd parameter takes either array of conditions or primary id value to update the rows. We can also set conditions this way.
```php
$data = [
    'name'  => 'amsify',
    'status'=> 'active'
];
$noOfRows = User::where('parent_id', 1)->update($data);
```
Can also update like this
```php
$data = [
    'name'  => 'amsify',
    'status'=> 'active'
];
$noOfRows = User::set($data)->where('parent_id', 1)->update();
```
It will return number of rows effected.

#### Delete
```php
$userId   = 10;
$noOfRows = User::delete($userId);
```
This method takes either array of conditions or primary id value to delete the rows.
```php
$conditions = ['parent_id' => 1];
$noOfRows = User::delete($conditions);
```
It will return number of rows effected.

#### Join
For joining the tables
```php
$users = User::select('*')->join('student')->on('user.id', '=', 'student.user_id')->all();
```
With alias
```php
$users = User::select(['u.*'])->alias('u')->join('student', 's')->on('u.id', '=', 's.user_id')->all();
```
You can also use these methods for different joins
```
leftJoin
rightJoin
fullOuterJoin
```
#### Raw
We can also pass part of raw query with methods
```php
$users = User::select(['id', 'NOW()'])->where('DATE(created_at)>2020-07-10')->all();
```
For insert
```php
$user = User::insert(['name' => 'amsify', 'created_at' => \PHPattern\DB::raw('NOW() - INTERVAL 1 DAY')]);
```

### 4. Relations
We can also set the model table relations with other models to get the related. Three types of relations we can set.
#### HasOne
```php
<?php

namespace App\Models;

use PHPattern\Database\Model;

class User extends Model
{
    public function categories()
    {
        return $this->hasMany('user_id', \App\Models\Category::class);
    }
}
```
The above definition of method will give us the rows belongs to `Category` model table whenever `categories` methods get called with the instance of each `User` model. Example for single user row
```php
$user = User::find(1);
$categories = $user->categories();
```
#### HasMany
```php
class User extends Model
{
    public function student()
    {
        return $this->hasOne('user_id', \App\Models\Student::class);
    }
}
```
This will return the single row of model class `Student`
```php
$user = User::find(1);
$student = $user->student();
```
#### BelongsTo
This works same as **HasOne** but in reverse order.
```php
class Category extends Model
{
    public function user()
    {
        return $this->belongsTo('user_id', \App\Models\User::class);
    }
}
```
```php
$category = Category::find(1);
$user = $category->user();
```