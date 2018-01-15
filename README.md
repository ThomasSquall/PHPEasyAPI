## API System for PHP

Easy to use library which take advantage of the [PHP7 annotations library](https://github.com/ThomasSquall/PHPMagicAnnotations).

### Installation

Using composer is quite simple, just run the following command:
``` sh
$ composer install thomas-squall/php-easy-api
```

### Usage Example

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

Let's analize:
1) We instantiated a Mailchimp object.
2) We filled the needed values for the call.
3) We instantianted a \PHPEasyAPI\Resolver object.
4) We called the `makeRequest` method of the resolver passing the Mailchimp object (which is a API Client as per annotations) and the name of the field containing the Endpoint annotation.
5) We printed the value of the field containing the endpoint of the annotation.

PS: Note that the result will be put inside the field itself.