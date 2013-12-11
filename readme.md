Buzzle
====

Extension for [Buzz](https://github.com/kriswallsmith/Buzz)

It currently extends the Buzz Browser and adds a caching layer with [DoctrineCache](https://github.com/doctrine/cache).

###Installation
add ivoba/buzzle to your composer requirements and:

    composer update ivoba/buzzle

###Usage
If you want to cache your Buzz requests, replace the Buzz/Browser with the Buzzle/Browser.

    $browser = new Buzzle/Browser();
    $browser->setCacher(new Doctrine/Common/Cache/PhpFileCache($cacheDir), new Buzzle/Validators/CacheValidator());
    $browser->call($url, $method, $headers, $content, $cacheLifetime);

###Features
- multiple cache backends like filesystem, redis, memcached etc. thanks to DoctrineCache
- caches only GET or HEAD
- caches only valid HTTP response status code ('200', '203', '204', '205', '300', '301', '410')
- adds a ```X-Buzzle-Cache: fresh``` header to the response, if from cache
- takes care of CacheControl headers
- CacheControl headers can be forced to be ignored: ```$Validator->setForceCache(true);```

###Todo
- unit tests!

###Disclaimer
- As the name suggests, you might better have a look at [guzzle](https://github.com/guzzle/guzzle) ;) since it has superpower.
- This lib is inspired by: https://github.com/dozoisch/CachedBuzzBundle

###Licence
MIT

