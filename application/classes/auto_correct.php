<?php

require_once(dirname(dirname(__FILE__)) . '/../vendors/smarty-street-auto-complete/src/SharedCredentials.php');
require_once(dirname(dirname(__FILE__)) . '/../vendors/smarty-street-auto-complete/src/SharedCredentials.php');
require_once(dirname(dirname(__FILE__)) . '/../vendors/smarty-street-auto-complete/src/ClientBuilder.php');
require_once(dirname(dirname(__FILE__)) . '/../vendors/smarty-street-auto-complete/src/US_Autocomplete_Pro/Lookup.php');
require_once(dirname(dirname(__FILE__)) . '/../vendors/smarty-street-auto-complete/src/US_Autocomplete_Pro/Client.php');

use SmartyStreets\PhpSdk\SharedCredentials;
use SmartyStreets\PhpSdk\ClientBuilder;
use SmartyStreets\PhpSdk\US_Autocomplete_Pro\Lookup;
use SmartyStreets\PhpSdk\US_Autocomplete_Pro\Suggestion;

class AutoCorrect extends FdObject
{
    public function run($address)
    {
        $key = '89221950819712832';
        $hostname = 'www.cargoflare.com';
        $sharedCredentials = new SharedCredentials($key, $hostname);

        $client = (new ClientBuilder($sharedCredentials))->withLicenses(["us-autocomplete-pro-cloud"])->buildUSAutocompleteProApiClient();

        $lookup = new Lookup($address);

        $client->sendLookup($lookup);

        $results = []; 
        foreach ($lookup->getResult() as $suggestion) {
            $results[] = $this->getAddress($suggestion);
        }

        return $results;
    }

    private function getAddress(Suggestion $suggestion)
    {
        return [
            'street' => $suggestion->getStreetLine(),
            'city' => $suggestion->getCity(),
            'state' => $suggestion->getState(),
            'zip' => $suggestion->getZipcode(),
        ];
    }
}
