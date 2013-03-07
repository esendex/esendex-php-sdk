Esendex PHP SDK
===============

## Installation

### Requirements
 - PHP >= 5.3.0
 - ext-curl enabled

**esendex-php-sdk** is available to install through several methods as well as from source.

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
pear config-set auto_discover 1
pear install esendex.github.com/pear/Esendex
```
We provide a PSR-0 autoloader for your to use for your convenience;
```php5
require_once 'Esendex/autoload.php';
```

#### GZIP
Download the current version [here](http://downloads.esendex.com.s3-website-eu-west-1.amazonaws.com/esendex-php-sdk/latest.sdk)
We provide a PSR-0 autoloader for your to use for your convenience;
```php5
require_once 'path/to/downloaded/Esendex/autoload.php';
```

## Getting Started

#### SMS Sending
```php5
$message = new \Esendex\Model\DispatchMessage(
    "WebApp", // Send from
    "01234567890", // Send to any valid number
    "My Web App is SMS enabled!",
    \Esendex\Model\Message::SmsType
);
$authentication = new \Esendex\Authentication\LoginAuthentication(
    "EX000000", "user@example.com", "secret"
);
$service = new \Esendex\DispatchService($authentication);
$result = $service->send($message);

print $result->id();
print $result->uri();
```

#### Retrieving Inbox Messages
```php5
$authentication = new \Esendex\Authentication\LoginAuthentication(
    "EX000000", "user@example.com", "secret"
);
$service = new \Esendex\InboxService($authentication);

$result = $service->latest();

print "Total Inbox Messages: {$result->totalCount()}";
print "Fetched: {$result->count()}";
foreach ($result as $message) {
    print "Message from: {$message->originator()}, {$message->summary()}";
}
```

#### Retrieving Full Message Body
```php5
$messageId = "unique-id-of-message";
$authentication = new \Esendex\Authentication\LoginAuthentication(
    "EX000000", "user@example.com", "secret"
);
$service = new \Esendex\MessageBodyService($authentication);

$result = $service->getMessageBodyById($messageId);

print $result;
```

## Would you like to know more?
Full REST API documentation can be found @ [developers.esendex.com](http://developers.esendex.com/)

## Issues
We hope you don't run into any issues but if you should please make use of the issue tracker on [github](https://github.com/esendex/esendex-php-sdk/issues) or send an email to [support@esendex.com](mailto:support@esendex.com)

## Feedback
Let us know what you think [@esendex](http://twitter.com/esendex)
