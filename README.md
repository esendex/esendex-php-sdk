Esendex PHP SDK
===============

## Installation

### Requirements
 - PHP >= 5.3.0
 - ext-curl enabled

The **esendex-php-sdk** is available to install through several methods as well as from source.

#### Composer
```json
{
    "require": {
        "esendex/sdk": "1.0.*"
    }
}
```

We're in the default [`Packagist`](http://packagist.org/packages/esendex/sdk) repository so just add the snippet above to your *composer.json*

Then just include the *autoload.php* somewhere in your code;
```php5
require_once 'path/to/vendor/autoload.php';
```

#### PEAR
Our package requires version 1.9.3 or newer
```bash
$ pear config-set auto_discover 1
$ pear install esendex.github.com/pear/Esendex
```
We provide a PSR-0 autoloader you can use for your convenience;
```php5
require_once 'Esendex/autoload.php';
```

#### GZIP
Download the current version [here](http://downloads.esendex.com.s3-website-eu-west-1.amazonaws.com/esendex-php-sdk/latest.sdk)

Our autoloader may be included somewhere in your application;
```php5
require_once 'path/to/downloaded/Esendex/autoload.php';
```

## Getting Started

#### Sending SMS
```php5
$message = new \Esendex\Model\DispatchMessage(
    "WebApp", // Send from
    "01234567890", // Send to any valid number
    "My Web App is SMS enabled!",
    \Esendex\Model\Message::SmsType
);
$authentication = new \Esendex\Authentication\LoginAuthentication(
    "EX000000", // Your Esendex Account Reference
    "user@example.com", // Your login email address
    "password" // Your password
);
$service = new \Esendex\DispatchService($authentication);
$result = $service->send($message);

print $result->id();
print $result->uri();
```

#### Retrieving Inbox Messages
```php5
$authentication = new \Esendex\Authentication\LoginAuthentication(
    "EX000000", // Your Esendex Account Reference
    "user@example.com", // Your login email address
    "password" // Your password
);
$service = new \Esendex\InboxService($authentication);

$result = $service->latest();

print "Total Inbox Messages: {$result->totalCount()}";
print "Fetched: {$result->count()}";
foreach ($result as $message) {
    print "Message from: {$message->originator()}, {$message->summary()}";
}
```
#### Track Message Status
```php5
$authentication = new \Esendex\Authentication\LoginAuthentication(
    "EX000000", // Your Esendex account reference
    "user@example.com", // Your login email
    "password" // Your password
);
$headerService = new \Esendex\MessageHeaderService($authentication);
$message = $headerService->message("messageId");
print_r($message->status());
```

#### Retrieving Full Message Body
```php5
$messageId = "unique-id-of-message";
$authentication = new \Esendex\Authentication\LoginAuthentication(
    "EX000000", // Your Esendex Account Reference
    "user@example.com", // Your login email address
    "password" // Your password
);
$service = new \Esendex\MessageBodyService($authentication);

$result = $service->getMessageBodyById($messageId);

print $result;
```

## Would you like to know more?
Full REST API documentation can be found @ [developers.esendex.com](http://developers.esendex.com/)

## Testing
#### Unit Tests
A suite of tests can be found in the `test` directory. To run them use the [phing](http://www.phing.info) build utility. e.g.
```bash
$ php phing-latest.phar
```

#### Credentials Test
You can check your account credentials using a phing task we have provided.
```bash
$ php phing-latest.phar check-access
Buildfile: /home/developer/esendex-php-sdk/build.xml

EsendexSDK > install_dependencies:


EsendexSDK > check-access:

Esendex Username ? user@example.com
Esendex Password ? secret
Account Reference? EX000000

    Account credentials OK!

BUILD FINISHED

Total time: 10.0000 seconds
``` 

## Issues
We hope you don't run into any issues but if you should please make use of the issue tracker on [github](https://github.com/esendex/esendex-php-sdk/issues) or send an email to [support@esendex.com](mailto:support@esendex.com)

## Feedback
Let us know what you think [@esendex](http://twitter.com/esendex)
