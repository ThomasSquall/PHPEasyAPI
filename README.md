# API System for PHP [![Tweet](https://img.shields.io/twitter/url/http/shields.io.svg?style=social)](https://twitter.com/intent/tweet?text=Easy%20to%20use%20PHP%20API%20libray!%20Check%20it%20out!%20&url=https://github.com/ThomasSquall/PHPEasyAPI&hashtags=php,api-server,api-client,developers)

Easy to use library which take advantage of the [PHP7 annotations library](https://github.com/ThomasSquall/PHPMagicAnnotations).

### Installation

Using composer is quite simple, just run the following command:
``` sh
$ composer require thomas-squall/php-easy-api
```

### Usage Example

#### Client

Let's assume we've got the following class:

``` php
<?php

/**
 * Class Mailchimp.
 * [\PHPEasyAPI\Client]
 */
class Mailchimp
{
    /**
     * [\PHPEasyAPI\Enrichment\Endpoint(method = "GET", url = "https://{$dataCenter}.api.mailchimp.com/3.0/lists/")]
     * [\PHPEasyAPI\Enrichment\User(username = "{$username}", password = "{$apiKey}")]
     */
    public $getLists;

    public $dataCenter;
    public $username;
    public $apiKey;
}
```

This is a basic implementation to call the `lists/` mailchimp endpoint.

PS: Note that `{$dataCenter}`, `{$username}` and `{$password}` special strings are used.
This special strings will evaluate with the class corresponding field value.

Now if we want to proceed and make the call we just need to do:

``` php
$mailchimp = new Mailchimp();
$mailchimp->dataCenter = '<YourDatacenter>';
$mailchimp->username = '<YourUsername>';
$mailchimp->apiKey = '<YourAPIKey>';

$resolver = new \PHPEasyAPI\Resolver();
$resolver->makeRequest($mailchimp, 'getLists');

print_r($mailchimp->getLists);
```

Let's analyze:
1) We instantiated a Mailchimp object.
2) We filled the needed values for the call.
3) We instantiated a \PHPEasyAPI\Resolver object.
4) We called the `makeRequest` method of the resolver passing the Mailchimp object (which is a API Client as per annotations) and the name of the field containing the Endpoint annotation.
5) We printed the value of the field containing the endpoint of the annotation.

PS: Note that the result will be put inside the field itself.

#### Server

Let's assume we've got the following class:

``` php
<?php

/**
 * Class Listener.
 * [\PHPEasyAPI\Server]
 */
class Listener
{
    /**
     * @param int $userId
     * @param int $listId
     * @return string
     * [\PHPEasyAPI\Enrichment\Endpoint(method = "GET", url = ":userID/getList/:listId")]
     */
    public function getList($userId, $listId)
    {
        return "List $listId of user $userId";
    }
}
```

And we want to listen for incoming calls at the `getList` method of the `user`.
To do that we need to bind the listener to an endpoint in that way:

``` php
$resolver->setBaseUrl('http://localhost/MyTest'); // Assuming this is your local test url.
$resolver->bindListener('user', new Listener()); // 'user' is the endpoint.
```

NB: The base url is needed to make the resolver understand which part of the request url do not compute.
Not setting it will throw an Exception as it is crucial for the system to work.

Now what remains to do is to resolve incoming requests:

``` php
$resolver->resolve('http://localhost/MyTest/user/10/getList/15'); // Replace this with the real request url.
```

Let's analyze:
1) We created a `Listener` class.
2) We annotated the class with the `\PHPEasyAPI\Server` annotation.
3) We created a method to handle calls and annotated with the `\PHPEasyAPI\Enrichment\Endpoint` annotation.
4) In the Endpoint annotation we passed the method (`GET` in this case) we wanted to handle and the url structure (Note that `:userId` ans `:listId` are placeholder and they will be substituted with the corresponding part of the url and passed to the function as parameters on the given order).
5) We bound the `'user'` endpoint to an instance of the `Listener`.
6) We resolved the requested url.