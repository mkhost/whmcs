<?php
// MKhost registrar module
// Supported domain extensions: .mk, .com.mk, .net.mk, .org.mk, .edu.mk, .inf.mk

// To install the module, put all files in WHMCS registrar module location under folder name: mkhost
// You will need to get ClientId and ClientSecret from https://portal.mkhost.com and configure them in WHMCS -> Setup -> Products/Services -> Domain Registrars
// ApiEndpoint = https://api.mkhost.com
// Support = domains@mkhost.com

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Domains\DomainLookup\ResultsList;
use WHMCS\Domains\DomainLookup\SearchResult;
use WHMCS\Module\Registrar\MKhostRegistrarModule\ApiClient;
use WHMCS\Module\Registrar\MKhostRegistrarModule\ApiClientCredentials;
use WHMCS\Module\Registrar\MKhostRegistrarModule\ApiEndPoint;

// Require any libraries needed for the module to function.
require_once __DIR__ . '/lib/ApiClient.php';
require_once __DIR__ . '/lib/ApiClientCredentials.php';
require_once __DIR__ . '/lib/ApiEndPoint.php';
// Also, perform any initialization required by the service's library.

function MKhost_MetaData()
{
    return array(
        'DisplayName' => 'MKhost Registrar Module for WHMCS',
        'APIVersion' => '1.0',
    );
}

function MKhost_getConfigArray()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'MKhost Registrar Module for WHMCS',
        ),
        'ClientId' => array(
            'Type' => 'text',
            'Size' => '300',
            'Default' => '',
            'Description' => 'Enter Client ID here',
        ),
        'ClientSecret' => array(
            'Type' => 'password',
            'Size' => '300',
            'Default' => '',
            'Description' => 'Enter Client Secret here',
        ),
        'ApiEndpoint' => array(
            'Type' => 'text',
            'Size' => '2000',
            'Default' => '',
            'Description' => 'Enter API Endpoint here',
        ),
    );
}


function MKhost_RegisterDomain($params)
{
    $clientId = $params['ClientId'];
    $clientSecret = $params['ClientSecret'];
    $apiEndPoint = $params['ApiEndpoint'];

    // Registration parameters
    $sld = $params['sld'];
    $tld = $params['tld'];
    $registrationPeriod = $params['regperiod'];

    // Registrant information
    $firstName = $params["firstname"];
    $lastName = $params["lastname"];
    $companyName = $params["companyname"];
    $email = $params["email"];
    $address1 = $params["address1"];
    $city = $params["city"];
    $postcode = $params["postcode"]; // Postcode/Zip code
    $countryCode = $params["countrycode"]; // eg. GB
    $phoneNumberFormatted = $params["fullphonenumber"]; // Format: +CC.xxxxxxxxxxxx

    // Admin contact information
    $adminFirstName = $params["adminfirstname"];
    $adminLastName = $params["adminlastname"];
    $adminEmail = $params["adminemail"];
    $adminCountry = $params["admincountry"]; // eg. GB
    $adminPhoneNumberFormatted = $params["adminfullphonenumber"]; // Format: +CC.xxxxxxxxxxxx

    // Tech contact information
    $techFirstName = $params["techfirstname"];
    $techLastName = $params["techlastname"];
    $techEmail = $params["techemail"];
    $techCountry = $params["techcountry"]; // eg. GB
    $techPhoneNumberFormatted = $params["techfullphonenumber"]; // Format: +CC.xxxxxxxxxxxx

    // Enbale DNS Managmenet for domain
    $enableDnsManagement = (bool)$params['dnsmanagement'];

    // Build post data
    $postFields = array(
        'domain' => $sld . '.' . $tld,
        'period' => $registrationPeriod,
        'name_servers' => [
            'ns1' => $params["ns1"],
            'ns2' => $params["ns2"],
            'ns3' => $params["ns3"],
            'ns4' => $params["ns4"],
            'ns5' => $params["ns5"]
        ],
        'contacts' => [
            'administrative' => [
                'name' => $adminFirstName . ' ' . $adminLastName,
                'email' => $adminEmail,
                'phone' => $adminPhoneNumberFormatted,
                'country' => $adminCountry
            ],
            'technical' => [
                'name' => $techFirstName . ' ' . $techLastName,
                'email' => $techEmail,
                'phone' => $techPhoneNumberFormatted,
                'country' => $techCountry
            ],
            'registrant' => [
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'phone' => $phoneNumberFormatted,
                'country' => $countryCode,
                'organization' => $companyName,
                'tax_number' => '',
                'address' => $address1,
                'city' => $city,
                'zip' => $postcode
            ],
        ],
        'dns_management' => $enableDnsManagement
    );

    try {
        $api = new ApiClient(
            new ApiClientCredentials($clientId, $clientSecret),
            new ApiEndPoint($apiEndPoint)
        );

        $api->post('/v1/shop/domains/register', $postFields);

        return array(
            'success' => true,
        );
    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}


function MKhost_TransferDomain($params)
{
    $clientId = $params['ClientId'];
    $clientSecret = $params['ClientSecret'];
    $apiEndPoint = $params['ApiEndpoint'];

    // Registration parameters
    $sld = $params['sld'];
    $tld = $params['tld'];
    $eppCode = $params['eppcode'];

    // Build post data
    $postFields = array(
        'domain' => $sld . '.' . $tld,
        'epp_code' => $eppCode
    );

    try {
        $api = new ApiClient(
            new ApiClientCredentials($clientId, $clientSecret),
            new ApiEndPoint($apiEndPoint)
        );

        $api->post('/v1/shop/domains/transfer', $postFields);

        return array(
            'success' => true,
        );
    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}


function MKhost_RenewDomain($params)
{
    $clientId = $params['ClientId'];
    $clientSecret = $params['ClientSecret'];
    $apiEndPoint = $params['ApiEndpoint'];

    // Registration parameters
    $sld = $params['sld'];
    $tld = $params['tld'];
    $registrationPeriod = $params['regperiod'];

    // Build post data
    $postFields = array(
        'domain' => $sld . '.' . $tld,
        'period' => $registrationPeriod
    );

    try {
        $api = new ApiClient(
            new ApiClientCredentials($clientId, $clientSecret),
            new ApiEndPoint($apiEndPoint)
        );

        $api->post('/v1/shop/domains/renew', $postFields);

        return array(
            'success' => true,
        );
    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}


function MKhost_GetNameservers($params)
{
    $clientId = $params['ClientId'];
    $clientSecret = $params['ClientSecret'];
    $apiEndPoint = $params['ApiEndpoint'];

    $domain = $params['sld'] . '.' . $params['tld'];

    try {
        $api = new ApiClient(
            new ApiClientCredentials($clientId, $clientSecret),
            new ApiEndPoint($apiEndPoint)
        );

        $api->get('/v1/domains/' . $domain . '/name-servers');

        $response = $api->getResponse();

        return array(
            'success' => true,
            'ns1' => $response['data']['name_servers']['list']['ns1'],
            'ns2' => $response['data']['name_servers']['list']['ns2'],
            'ns3' => $response['data']['name_servers']['list']['ns3'],
            'ns4' => $response['data']['name_servers']['list']['ns4'],
            'ns5' => $response['data']['name_servers']['list']['ns5'],
        );
    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}


function MKhost_SaveNameservers($params)
{
    $clientId = $params['ClientId'];
    $clientSecret = $params['ClientSecret'];
    $apiEndPoint = $params['ApiEndpoint'];

    $domain = $params['sld'] . '.' . $params['tld'];

    // Submitted nameserver values
    $nameserver1 = $params['ns1'];
    $nameserver2 = $params['ns2'];
    $nameserver3 = $params['ns3'];
    $nameserver4 = $params['ns4'];
    $nameserver5 = $params['ns5'];

    // Build post data
    $postFields = array(
        'name_servers' => [
            'ns1' => $nameserver1,
            'ns2' => $nameserver2,
            'ns3' => $nameserver3,
            'ns4' => $nameserver4,
            'ns5' => $nameserver5,
        ]
    );
    try {
        $api = new ApiClient(
            new ApiClientCredentials($clientId, $clientSecret),
            new ApiEndPoint($apiEndPoint)
        );

        $api->put('/v1/domains/' . $domain . '/update-name-servers', $postFields);

        return array(
            'success' => true,
        );
    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}


function MKhost_GetContactDetails($params)
{
    $clientId = $params['ClientId'];
    $clientSecret = $params['ClientSecret'];
    $apiEndPoint = $params['ApiEndpoint'];

    $domain = $params['sld'] . '.' . $params['tld'];

    try {
        $api = new ApiClient(
            new ApiClientCredentials($clientId, $clientSecret),
            new ApiEndPoint($apiEndPoint)
        );

        $api->get('/v1/domains/' . $domain . '/register-contacts');

        $response = $api->getResponse();

        $registerContact = [];
        $technicalContact = [];
        $administrativeContact = [];

        foreach ($response['data']['register_contacts'] as $contact) {
            if ($contact['type'] === 'registrant') {
                $registerContact = $contact;
            }

            if ($contact['type'] === 'technical') {
                $technicalContact = $contact;
            }

            if ($contact['type'] === 'administrative') {
                $administrativeContact = $contact;
            }
        }

        return array(
            'Registrant' => array(
                'First Name' => $registerContact['first_name'],
                'Last Name' => $registerContact['last_name'],
                'Company Name' => $registerContact['company'],
                'Email Address' => $registerContact['email'],
                'Address 1' => $registerContact['address1'],
                'Address 2' => $registerContact['address2'],
                'City' => $registerContact['city'],
                'State' => $registerContact['state_region'],
                'Postcode' => $registerContact['zip'],
                'Country' => $registerContact['country']['iso2'],
                'Phone Number' => $registerContact['phone'],
                'Fax Number' => '',
            ),
            'Technical' => array(
                'First Name' => $technicalContact['first_name'],
                'Last Name' => $technicalContact['last_name'],
                'Company Name' => $technicalContact['company'],
                'Email Address' => $technicalContact['email'],
                'Address 1' => $technicalContact['address1'],
                'Address 2' => $technicalContact['address2'],
                'City' => $technicalContact['city'],
                'State' => $technicalContact['state_region'],
                'Postcode' => $technicalContact['zip'],
                'Country' => $technicalContact['country']['iso2'],
                'Phone Number' => $technicalContact['phone'],
                'Fax Number' => '',
            ),
            'Admin' => array(
                'First Name' => $administrativeContact['first_name'],
                'Last Name' => $administrativeContact['last_name'],
                'Company Name' => $administrativeContact['company'],
                'Email Address' => $administrativeContact['email'],
                'Address 1' => $administrativeContact['address1'],
                'Address 2' => $administrativeContact['address2'],
                'City' => $administrativeContact['city'],
                'State' => $administrativeContact['state_region'],
                'Postcode' => $administrativeContact['zip'],
                'Country' => $administrativeContact['country']['iso2'],
                'Phone Number' => $administrativeContact['phone'],
                'Fax Number' => '',
            ),
        );
    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}


function MKhost_SaveContactDetails($params)
{
    $clientId = $params['ClientId'];
    $clientSecret = $params['ClientSecret'];
    $apiEndPoint = $params['ApiEndpoint'];

    $domain = $params['sld'] . '.' . $params['tld'];

    $contactDetails = $params['contactdetails'];

    try {
        $api = new ApiClient(
            new ApiClientCredentials($clientId, $clientSecret),
            new ApiEndPoint($apiEndPoint)
        );

        $api->put('/v1/contacts/change-administrative-contact-for-domain', [
            'domain' => $domain,
            'email' => $contactDetails['Admin']['Email Address'],
            'first_name' => $contactDetails['Admin']['First Name'],
            'last_name' => $contactDetails['Admin']['Last Name'],
            'company' => $contactDetails['Admin']['Company Name'],
            'address1' => $contactDetails['Admin']['Address 1'],
            'address2' => $contactDetails['Admin']['Address 2'],
            'city' => $contactDetails['Admin']['City'],
            'state' => $contactDetails['Admin']['State'],
            'zip' => $contactDetails['Admin']['Postcode'],
            'country_id' => $contactDetails['Admin']['Country'],
            'phone' => $contactDetails['Admin']['Phone Number']
        ]);

        $api->put('/v1/contacts/change-technical-contact-for-domain', [
            'domain' => $domain,
            'email' => $contactDetails['Technical']['Email Address'],
            'first_name' => $contactDetails['Technical']['First Name'],
            'last_name' => $contactDetails['Technical']['Last Name'],
            'company' => $contactDetails['Technical']['Company Name'],
            'address1' => $contactDetails['Technical']['Address 1'],
            'address2' => $contactDetails['Technical']['Address 2'],
            'city' => $contactDetails['Technical']['City'],
            'state' => $contactDetails['Technical']['State'],
            'zip' => $contactDetails['Technical']['Postcode'],
            'country_id' => $contactDetails['Technical']['Country'],
            'phone' => $contactDetails['Technical']['Phone Number']
        ]);

        return array(
            'success' => true,
        );
    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}


function MKhost_GetEPPCode($params)
{
    $clientId = $params['ClientId'];
    $clientSecret = $params['ClientSecret'];
    $apiEndPoint = $params['ApiEndpoint'];

    $domain = $params['sld'] . '.' . $params['tld'];

    try {
        $api = new ApiClient(
            new ApiClientCredentials($clientId, $clientSecret),
            new ApiEndPoint($apiEndPoint)
        );

        $api->get('/v1/domains/' . $domain . '/request-epp-transfer-code');

        $response = $api->getResponse();
        $eppCode = $response['data']['epp_transfer_code'];

        if ($eppCode) {
            // If EPP Code is returned, return it for display to the end user
            return array(
                'eppcode' => $eppCode,
            );
        }
    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}


function MKhost_ClientArea($params)
{
    $output
        = '
        <div class="alert alert-info">
            MKhost Registrar Module
        </div>
    ';
    return $output;
}
