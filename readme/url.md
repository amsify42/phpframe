## URL
This is a helper for generating absolute urls.
To get the current url, we can do
```php
\PHPattern\URL::current();
```
To get the full url with passed request uri
```php
\PHPattern\URL::get('user/info'); // It will return absolute url along with the uri param passed
```
Example: `http://www.site.com/user/info` will be returned if `http://www.site.com/` is the base url.
<br/>
We can also pass url parameters as array to get the generated url
```php
\PHPattern\URL::get('user/details', ['name' => 'amsify', 'id' => 123]);
```
Example: `http://www.site.com/user/details?name=amsify&id=123` will be returned if `http://www.site.com/` is the base url.
<br/>
<br/>
Can also pass the same for current url
```php
\PHPattern\URL::current(['name' => 'amsify', 'id' => 123]);
```