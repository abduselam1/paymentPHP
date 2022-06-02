# Payment gateway SDK for laravel developers

this is a package for PHP (Laravel ) developers to interate their system to our payment gateway

before using the package please goto our website and register as a merchant

after creating an acount goto the app page and create your app


#installation

`composer require abdu/paymentgateway`

in your .env file put your credentials

```
PAYMENT_GATEWAY_KEY=key
PAYMENT_GATEWAY_USERNAME=username
PAYMENT_GATEWAY_PASSWORD=**********

```
And check in your config/app.php file that the _cipher property set to _AES-256-CBC

#usage

In your controller import the package

```php
use Abdu\Paymentgateway\Payment;

public function showBalance(){
    $payment = new Payment();
    
    return payment->queryBalance();
}
```

# functions

## checkout

If you need your customer to pay you call this function

```php
    $payment->checkout($amount,$redirect_url,$error_redirect_url) 
```

### parameters
#### amount _required | float
you have to set an amonut of money that you need to accept from your client 

### redirect_url required | string
a return url after a successful operation

### error_redirect_url required | string
a return url after a failed operation


## queryBalance
whenever you need to query your balance call this method no parameter needed
```php
    $payment->queryBalance() 
```

##getInvoice
Every time you need a single invoice fire this method
```php
$payment->getInvoice($transaction_id) 
```

### parameters
#### transaction_id _required
you have to set an int of transaction id 

## invoice
if you need to retriev a collection of invoices

#### parameters
<!-- * *start* - _datetime |_optional put your start time to filter over your invoices 
* *end* - _datetime|_optional put your start time to filter over your invoices -->
* *year* - int|string|_optional you can get any year invoices like 2022,2021 but if you set start time we override the year with start and 
* *paginate* - bool|_optional If you need to paginate your records (default false)
* *paginatePerpage* - int|_optional how many records do you need per page in the pagination (default 10)
```php
    $payment->invoice()
```


## send
every time you need to send a money to a person or people you can use this method

#### parameters
* *address* - _string | _array | _required - an address is a phone number of a customer who is using our payment gateway or a collection of phone number stored in array *phone number* must start with +2519.......

* *amount* - _float | _array | _required  the amount of money an address can get if its a float value the amount transfered to the address if its an array the amount of money transfered with respect to the adress

```php
    //option 1 single adress
    $address = "+251917949637";
    $amount = 4223.02;

    $payment->send($address,$amount);

    //option 2 multiple address and constant amount

    $address = ["+251917949637","+251929194872"];
    $amount = 4223.02;

    $payment->send($address,$amount);

    //option 2 multiple address with different amount

    $address = ["+251917949637","+251929194872"];
    $amount = [4223.02,488.98];

    $payment->send($address,$amount);
```
