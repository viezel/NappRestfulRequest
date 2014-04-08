# Napp Laravel Restful Request

An extension to \Illuminate\Http\Request used in Laravel. 

It makes the Request class fully Restful by making it possible to use PUT/PATCH/DELETE. 


## Installation

You can install the package for your Laravel 4 project through Composer.

Require the package in your `composer.json`.

```
"napp/restful-request": "1.*"
```

Run composer to install or update the package.

```bash
$ composer update
```

Add it to the bootstrap in `bootstrap/start.php`.

```php
use Illuminate\Foundation\Application;
Application::requestClass('Napp\Extensions\Request');

```

## Usage

Just use the it as you normally would. Nothing changed. 

Example:

```php
Route::resource('todos', 'TodoController', array('except' => array('create', 'edit')));
```


## Author

**Napp ApS**  
web: http://www.napp.dk  
email: mm@napp.dk  
twitter: @nappdev  

## License

    Copyright (c) 2010-2013 Mads Møller

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
