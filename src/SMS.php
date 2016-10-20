<?php

namespace Enniel\Epochta;

use BadMethodCallException;
use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate;

class SMS
{
    const HOST = 'http://atompark.com/api/sms';
    const VERSION = '3.0';

    /**
     * Actions.
     *
     * @var array
     */
    protected $actions = [
        'addAddressBook',
        'delAddressBook',
        'editAddressBook',
        'getAddressBook',
        'searchAddressBook',
        'cloneAddressBook',
        'addPhoneToAddressBook',
        'getPhoneFromAddressBook',
        'delPhoneFromAddressBook',
        'delPhoneFromAddressBookGroup',
        'editPhone',
        'searchPhones',
        'addPhoneToExceptions',
        'delPhoneFromExceptions',
        'editExceptions',
        'getException',
        'searchPhonesInExceptions',
        'getUserBalance',
        'registerSender',
        'getSenderStatus',
        'createCampaign',
        'sendSMS',
        'sendSMSGroup',
        'getCampaignInfo',
        'getCampaignDeliveryStats',
        'cancelCampaign',
        'deleteCampaign',
        'checkCampaignPrice',
        'checkCampaignPriceGroup',
        'getCampaignList',
        'getCampaignDeliveryStatsGroup',
        'getTaskInfo',
    ];

    /**
     * Aliases.
     *
     * @var array
     */
    protected $aliases = [
        'addAddressBook'                => 'addAddressbook',
        'delAddressBook'                => 'delAddressbook',
        'editAddressBook'               => 'editAddressbook',
        'getAddressBook'                => 'getAddressbook',
        'cloneAddressBook'              => 'cloneaddressbook',
        'delPhoneFromAddressBookGroup'  => 'delphonefromaddressbookgroup',
        'sendSMSGroup'                  => 'sendsmsgroup',
        'getCampaignDeliveryStatsGroup' => 'getcampaigndeliverystatsgroup',
        'getTaskInfo'                   => 'gettaskinfo',
    ];

    /**
     * Configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Call method.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, array $parameters = [])
    {
        $parameters = array_key_exists(0, $parameters) ? $parameters[0] : [];
        $parameters = is_array($parameters) ? $parameters : [];
        $action = $this->action($method);
        if (! is_null($action)) {
            return self::dispatch($action, $parameters);
        }
        throw new BadMethodCallException("Method {$method} does not exist.");
    }

    /**
     * Dispatch.
     *
     * @param  string $action
     * @param  array $parameters
     *
     * @return mixed
     */
    public function dispatch($action, array $parameters = [])
    {
        $parameters['key'] = $this->config('public_key');
        $parameters['sum'] = $this->summary(array_merge($parameters, [
            'action'  => $action,
            'version' => self::VERSION,
        ]));
        $uri = (new UriTemplate())->expand(self::HOST.'{/segments*}{?parameters*}', [
            'segments'   => [
                self::VERSION, $action,
            ],
            'parameters' => $parameters,
        ]);

        return (new Client())->get($uri);
    }

    /**
     * Generate control summary.
     *
     * @param  array  $parameters
     *
     * @return string
     */
    protected function summary(array $parameters = [])
    {
        ksort($parameters);
        $summary = '';
        foreach ($parameters as $value) {
            $summary .= $value;
        }
        $summary .= $this->config('private_key');

        return md5($summary);
    }

    /**
     * Get value from configuration.
     *
     * @param  string $key
     *
     * @return mixed
     */
    protected function config($key)
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
    }

    /**
     * Get action.
     *
     * @param  string $action
     *
     * @return string
     */
    protected function action($action)
    {
        $action = in_array($action, $this->actions) ? $action : null;
        $action = array_key_exists($action, $this->aliases) ? $this->aliases[$action] : $action;

        return $action;
    }
}
