<?php

namespace Koolm\Bittrex;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;

class BittrexClient
{
    const API_URL = 'https://bittrex.com/api/v1.1/';

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var Client
     */
    private $client;

    /**
     * BittrexClient constructor.
     *
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->client = new Client([
            'base_uri' => static::API_URL,
            'timeout'  => 20
        ]);
    }

    /**
     * @return object
     */
    public function getMarkets()
    {
        return $this->publicRequest('getmarkets');
    }

    /**
     * @return object
     */
    public function getCurrencies()
    {
        return $this->publicRequest('getcurrencies');
    }

    /**
     * @param string $market
     *
     * @return object
     */
    public function getTicker($market)
    {
        return $this->publicRequest('getticker', compact('market'));
    }

    /**
     * @return object
     */
    public function getMarketSummaries()
    {
        return $this->publicRequest('getmarketsummaries');
    }

    /**
     * @param string $market
     *
     * @return object
     */
    public function getMarketSummary($market)
    {
        return $this->publicRequest('getmarketsummary', compact('market'));
    }

    /**
     * @param string $market
     * @param string $type
     *
     * @return object
     */
    public function getOrderBook($market, $type = 'both')
    {
        return $this->publicRequest('getorderbook', compact('market', 'type'));
    }

    /**
     * @param string $market
     *
     * @return object
     */
    public function getMarketHistory($market)
    {
        return $this->publicRequest('getmarkethistory', compact('market'));
    }

    /**
     * @return object
     */
    public function getBalances()
    {
        return $this->accountRequest('getbalances');
    }

    /**
     * @param string $currency
     *
     * @return object
     */
    public function getBalance($currency)
    {
        return $this->accountRequest('getbalance', compact('currency'));
    }

    /**
     * @param string $currency
     *
     * @return object
     */
    public function getDepositAddress($currency)
    {
        return $this->accountRequest('getdepositaddress', compact('currency'));
    }

    /**
     * @param string $currency
     * @param int $quantity
     * @param string $address
     * @param string $paymentid
     *
     * @return object
     */
    public function withdraw($currency, $quantity, $address, $paymentid = null)
    {
        return $this->accountRequest('withdraw', compact('currency', 'quantity', 'address', 'paymentid'));
    }

    /**
     * @param string $uuid
     *
     * @return object
     */
    public function getOrder($uuid)
    {
        return $this->accountRequest('getorder', compact('uuid'));
    }

    /**
     * @param string $market
     *
     * @return object
     */
    public function getOrderHistory($market = null)
    {
        return $this->accountRequest('getorderhistory', compact('market'));
    }

    /**
     * @param string $currency
     *
     * @return object
     */
    public function getWithdrawalHistory($currency = null)
    {
        return $this->accountRequest('getwithdrawalhistory', compact('currency'));
    }

    /**
     * @param string $currency
     *
     * @return object
     */
    public function getDepositHistory($currency = null)
    {
        return $this->accountRequest('getdeposithistory', compact('currency'));
    }

    /**
     * @param string $market
     * @param int $quantity
     * @param int $rate
     *
     * @return object
     */
    public function buyLimit($market, $quantity, $rate)
    {
        return $this->marketRequest('buylimit', compact('market', 'quantity', 'rate'));
    }

    /**
     * @param string $market
     * @param int $quantity
     * @param int $rate
     *
     * @return object
     */
    public function sellLimit($market, $quantity, $rate)
    {
        return $this->marketRequest('selllimit', compact('market', 'quantity', 'rate'));
    }

    /**
     * @param string $uuid
     *
     * @return object
     */
    public function cancel($uuid)
    {
        return $this->marketRequest('cancel', compact('uuid'));
    }

    /**
     * @param string $market
     *
     * @return object
     */
    public function getOpenOrders($market)
    {
        return $this->marketRequest('getopenorders', compact('market'));
    }

    /**
     * Make an public API request.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return object
     */
    protected function publicRequest($endpoint, $params = [])
    {
        return $this->request('public/' . $endpoint, $params);
    }

    /**
     * Make an private account API request.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return object
     */
    protected function accountRequest($endpoint, $params = [])
    {
        return $this->privateRequest('account/' . $endpoint, $params);
    }

    /**
     * Make an private market API request.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return object
     */
    protected function marketRequest($endpoint, $params = [])
    {
        return $this->privateRequest('market/' . $endpoint, $params);
    }

    /**
     * Make an API request.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return object
     */
    private function request($endpoint, $params = [])
    {
        $path = $endpoint . '?' . http_build_query($params);

        $request = $this->client->get($path);

        return json_decode(
            $request->getBody()->getContents()
        );
    }

    /**
     * Make an signed API request providing a key and nonce.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return object
     */
    private function privateRequest($endpoint, $params = [])
    {
        $params['apikey'] = $this->key;
        $params['nonce'] = time();

        $path = $endpoint . '?' . http_build_query($params);
        $sign = hash_hmac('sha512', static::API_URL . $path, $this->secret);

        $request = $this->client->get($path, [
            'headers' => ['apisign' => $sign]
        ]);

        return json_decode(
            $request->getBody()->getContents()
        );
    }
}
