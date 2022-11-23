<?php
/**************************************************************************************************
 * Location class                                                                                                                                                        *
 * This class represent one location                                                                                                                            *
 *                                                                                                                                                                            *
 * Client:        FreightDragon                                                                                                                                    *
 * Version:        1.0                                                                                                                                                    *
 * Date:            2011-09-28                                                                                                                                        *
 * Author:        C.A.W., Inc. dba INTECHCENTER                                                                                                            *
 * Address:    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                                                                    *
 * E-mail:        techsupport@intechcenter.com                                                                                                            *
 * CopyRight 2011 FreightDragon. - All Rights Reserved                                                                                                *
 ***************************************************************************************************/

/**
 * Class Location
 *
 * @property int $id
 * @property string $name
 * @property string $auction_name
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $company
 * @property string $phone1
 * @property string $phone2
 * @property string $phone3
 * @property string $phone_cell
 */
abstract class Location extends FdObject {
	const TABLE = "app_locations";

	public static $attributeTitles = array(
		'id' => 'ID',
		'name' => 'Name',
		'auction_name' => 'Auction Name',
		'address1' => 'Address',
		'address2' => 'Address 2',
		'city' => 'City',
		'state' => 'State',
		'zip' => 'Zip',
		'country' => 'Country',
		'company' => 'Company',
		'phone1' => 'Phone (1)',
		'phone2' => 'Phone (2)',
		'phone3' => 'Phone (3)',
		'phone_cell' => 'Phone (Cell)'
	);

	public function getLink() {
		$link = "http://maps.google.com/maps?q=" . urlencode($this->city . ",+" . $this->state);
		return $link;
	}

	public function getFormatted($type = 'short') {
		switch ($type) {
			case 'short':
				return trim($this->city.', '.$this->state.' '.$this->zip, ", ");
				break;
			default:
				return null;
				break;
		}
	}
}