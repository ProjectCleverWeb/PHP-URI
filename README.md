# PHP URI [![Build Status](https://travis-ci.org/ProjectCleverWeb/PHP-URI.svg?branch=master&style=flat)](https://travis-ci.org/ProjectCleverWeb/PHP-URI) [![Code Coverage](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/badges/coverage.png?b=master&style=flat)](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/badges/quality-score.png?b=master&style=flat)](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/?branch=master) [![License](https://poser.pugx.org/projectcleverweb/php-uri/license.svg?style=flat)](http://opensource.org/licenses/MIT)

A PHP library for working with URI's, that is designed around the URI standard. Requires PHP `5.4` or later. This library replaces and extends all of PHP's `parse_url()` features, and even has some handy aliases.

Copyright (c) 2014 Nicholas Jordon - All Rights Reserved.

### Installing The Library

#### Composer:

Add this to your composer.json file

```json
"require": {
	"projectcleverweb/php-uri":"~1.0"
}
```

#### Manual:

**Download:**<br>
[![Latest Stable Version](https://poser.pugx.org/projectcleverweb/php-uri/v/stable.svg?style=flat)](https://github.com/ProjectCleverWeb/PHP-URI/releases/tag/1.0.0) [![Latest Unstable Version](https://poser.pugx.org/projectcleverweb/php-uri/v/unstable.svg?style=flat)](https://github.com/ProjectCleverWeb/PHP-URI/archive/master.zip)

Just include the `uri.lib.php` file somewhere in your application.

## Examples

#### Example #1: String Operations

```php
$uri = new uri('http://example.com/path/to/file.ext');

$uri->replace('QUERY', 'number=3');
$uri->replace('PATH', '/foo/bar');
$uri->append('PATH', '.baz');
$new = $uri->prepend('HOST', 'www.');

$uri->reset();
$original = $uri->str();

$uri->replace('FRAGMENT', 'Checkout');
$secure = $uri->replace('SCHEME_NAME', 'https');

echo $new.PHP_EOL;
echo $original.PHP_EOL;
echo $secure;
```

**Output:**
```html
http://www.example.com/foo/bar.baz?number=3
http://example.com/path/to/file.ext
https://example.com/path/to/file.ext#Checkout
```

#### Example #2: Daisy Chaining Operations

Need to change a lot while keeping anything extra intact? Chain it.

```php
$uri1 = new uri('ftp://jdoe:pass1234@my-server.com/public_html');

// Lets upgrade to an admin account under sftp, but stay in the current directory.
$uri1->chain()->
	prepend('SCHEME_NAME', 's')->
	replace('PORT', '22')->
	replace('USER', 'admin')->
	replace('PASS', 'secure-pass-123');

// NOTE: chain() methods always return the chain object, even if a method fails.
echo $uri1;

// Any failure results in the chain() error count geting incremented.
if (0 < $uri->chain()->error_count) {
	print_f('The chain failed %1$s times!', $uri->chain()->error_count);
}
```

**Output:**
```html
sftp://admin:secure-pass-123@my-server.com:22/public_html
```

#### Example #3: Information Gathering

```php
$uri = new uri('http://example.com/path/to/file.ext?q=1');

if ($uri->scheme_name == 'https') {
	echo 'Uses SSL'.PHP_EOL;
} else {
	echo 'Does not use SSL'.PHP_EOL;
}

// Change to an absolute path
$abs_path = $_SERVER['DOCUMENT_ROOT'].$uri->path;
echo $abs_path.PHP_EOL;

// easier to read links
printf('<a href="%1$s">%2$s</a>', $uri->str(), $uri->host.$uri->path);

// FTP logins
$uri = new uri('ftp://jdoe@example.com/my/home/dir');
$login = array(
	'username' => $uri->user,
	'password' => $user_input,
	'domain'   => $uri->host,
	'path'     => $uri->path
);
```

**Output:**
```html
Does not use SSL
/var/www/path/to/file.ext
<a href="http://example.com/path/to/file.ext?q=1">example.com/path/to/file.ext</a>
```

#### Example #4: Works With A Wide Range Of URIs

Works perfectly with email, skype, and ssh URIs. The parser is based directly off the URI standard, so it will work well with uncommon and new URI types.

```php
$uri1 = new uri('git@github.com:ProjectCleverWeb/PHP-URI.git');
$uri2 = new uri('example@gmail.com');

// Publish you source to multiple services?
echo $uri1.PHP_EOL; // PHP will automatically get the current URI
echo $uri1->replace('HOST', 'gitlab.com').PHP_EOL;
echo $uri1->replace('HOST', 'bitbucket.org').PHP_EOL.PHP_EOL;

// Quick and easy email template URI
$uri2->chain()
	->replace('SCHEME', 'mailto:')
	->query_replace('subject', 'Re: [Suggestion Box]')
	->query_replace('body', 'More snickers in the break room please!')
;
printf('<a href="%1$s">%2$s</a>', $uri2, $uri2->authority);
```

**Output:**
```html
git@github.com:ProjectCleverWeb/PHP-URI.git
git@gitlab.com:ProjectCleverWeb/PHP-URI.git
git@bitbucket.org:ProjectCleverWeb/PHP-URI.git

<a href="mailto:example@gmail.com?subject=Re%3A%20%5BSuggestion%20Box%5D&body=More%20snickers%20in%20the%20break%20room%20please%21">example@gmail.com</a>
```

## Known Issues

- Cloning doesn't work as expected (use `$clone = new \uri($original->str()); $clone->input = $original->input;` instead)
- You cannot directly change the authority of a URI. This is intentional, as the authority is generated from the current URI.

## License

>The MIT License (MIT)
>
>Copyright (c) 2014 Nicholas Jordon - All Rights Reserved
>
>Permission is hereby granted, free of charge, to any person obtaining a copy
>of this software and associated documentation files (the "Software"), to deal
>in the Software without restriction, including without limitation the rights
>to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
>copies of the Software, and to permit persons to whom the Software is
>furnished to do so, subject to the following conditions:
>
>The above copyright notice and this permission notice shall be included in
>all copies or substantial portions of the Software.
>
>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
>IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
>FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
>AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
>LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
>OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
>THE SOFTWARE.