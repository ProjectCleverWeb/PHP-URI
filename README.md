#PHP URI [![Build Status](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ProjectCleverWeb/PHP-URI/?branch=master)#

A PHP library for working with URI's. Requires PHP `5.3.7` or later. Replaces and extends PHP's `parse_url()`.

Copyright &copy; 2014 Nicholas Jordon - All Rights Reserved <br>
Licensed under the MIT license

####Download: [1.0.0 RC1](https://github.com/ProjectCleverWeb/PHP-URI/archive/master.zip)####

---

##Examples##

####Example #1: String Operations###

```php
<?php
$uri = new uri('http://example.com/path/to/file.ext');

$uri->replace('QUERY', array('rand' => (string) rand(1, 10)));
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
?>
```

**Output:**
```
http://www.example.com/foo/bar.baz?rand=6
http://example.com/path/to/file.ext
https://example.com/path/to/file.ext#Checkout
```

####Example #2: Information Gathering####

```php
<?php
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
$link = sprintf('<a href="%1$s">%2$s</a>', $uri->str(), $uri->host.$uri->path);
echo $link;

// FTP logins
$uri = new uri('ftp://jdoe@example.com/my/home/dir');
$login = array(
	'username' => $uri->user,
	'password' => $user_input,
	'domain'   => $uri->host,
	'path'     => $uri->path
);
?>
```

**Output:**
```
Does not use SSL
/var/www/path/to/file.ext
<a href="http://example.com/path/to/file.ext?q=1">example.com/path/to/file.ext</a>
```

####Example #3: Works With A Wide Range Of URIs####

Works perfectly with email, skype, and ssh URIs. The parser is based directly off the URI standard, so it will work well with uncommon and new URI types.

```php
<?php
$uri1 = new uri('git@github.com:ProjectCleverWeb/PHP-URI.git');
$uri2 = new uri('example@gmail.com');
$uri3 = new uri('ftp://jdoe:pass1234@my-server.com/public_html');

// Publish you source to multiple services?
echo $uri1.PHP_EOL; // PHP will automatically get the current URI
echo $uri1->replace('HOST', 'gitlab.com').PHP_EOL;
echo $uri1->replace('HOST', 'bitbucket.org').PHP_EOL.PHP_EOL;

// Quick and easy email template URI
$uri2->replace('SCHEME', 'mailto:');
printf(
	'<a href="%1$s">%2$s</a>'.PHP_EOL.PHP_EOL,
	$uri2->replace('QUERY', array(
		'subject' => 'Re: [Suggestion Box]',
		'body'    => 'More snickers in the break room please!'
	)),
	$uri2->authority
);

// change FTP user/path/port/type
$uri3->replace('SCHEME_NAME', 'sftp');
$uri3->replace('PATH', '/admin/configuration');
$uri3->replace('PORT', '22');
$uri3->replace('USER', 'admin');
echo $uri3->replace('PASS', 'secure-pass-123');
?>
```

**Output:**
```
git@github.com:ProjectCleverWeb/PHP-URI.git
git@gitlab.com:ProjectCleverWeb/PHP-URI.git
git@bitbucket.org:ProjectCleverWeb/PHP-URI.git

<a href="mailto:example@gmail.com?subject=Re%3A%20%5BSuggestion%20Box%5D&body=More%20snickers%20in%20the%20break%20room%20please%21">example@gmail.com</a>

sftp://admin:secure-pass-123@my-server.com:22/admin/configuration
```

##License##

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

