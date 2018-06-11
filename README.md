# Laravel Bittrex

Communicate with the Bittrex API.

## Configuation
Publish the default configuration:

    php artisan vendor:publish --provider="Koolm\Bittrex\BittrexServiceProvider"

Add your Bittrex client credentials to the environment variables:

    BITTREX_KEY=key
    BITTREX_SECRET=secret

## Usage

    use Koolm\Bittrex\Facades\Bittrex;
    
    // Public API
    Bittrex::getMarkets();
    Bittrex::getCurrencies();
    Bittrex::getTicker($market);
    Bittrex::getMarketSummaries();
    Bittrex::getMarketSummary($market);
    Bittrex::getOrderBook($market, $type);
    Bittrex::getMarketHistory($market);
    
    // Market API
    Bittrex::buyLimit($market, $quantity, $rate);
    Bittrex::sellLimit($market, $quantity, $rate);
    Bittrex::cancelOrder($uuid);
    Bittrex::getOpenOrders($market);
    
    // Account API
    Bittrex::getBalances();
    Bittrex::getBalance($currency);
    Bittrex::getDepositAddress($currency);
    Bittrex::withdraw($currency, $quantity, $address, $paymentid);
    Bittrex::getOrder($uuid);
    Bittrex::getOrderHistory($market);
    Bittrex::getWithdrawalHistory($currency);
    Bittrex::getDepositHistory($currency);
    
See https://bittrex.com/Home/Api for the documentation.