<?php
/* * ************************************************************************************************
 * Client:  CargoFalre
 * Version: 2.0
 * Date:    2011-04-26
 * Author:  CargoFlare Team
 * Address: 7252 solandra lane tamarac fl 33321
 * E-mail:  stefano.madrigal@gmail.com
 * CopyRight 2021 Cargoflare.com - All Rights Reserved
 * ************************************************************************************************** */
 
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
/**
 * @var Daffny $daffny
 * @var AppAction $action
 */

/**
 * @param array $data
 * @param Daffny $daffny
 *
 * @return mixed|string
 */
function generateInvoice($data, $daffny)
{
    $daffny->tpl->path = ROOT_PATH . "app/templates/";
    $daffny->tpl->short = true;
    $product = new Product($daffny->DB);
    $product->load($data['product']);
    if ($data['number'] > 0) {
        $additional = new Product($daffny->DB);
        $additional->load($data['additional']);
    }
    if ($data['storage'] > 0) {
        $storage = new Product($daffny->DB);
        $storage->load($data['storage']);
    }

    if ($data['addon_aq'] > 0) {
        $addon_aq = new Product($daffny->DB);
        $addon_aq->load($data['addon_aq']);
    }
    $daffny->tpl->products = array(
        array(
            'item' => $product->code,
            'product' => $product->name,
            'quantity' => 1,
            'price' => $product->price,
            'total' => $product->price,
        )
    );
    if (isset($additional)) {
        $daffny->tpl->products[] = array(
            'item' => $additional->code,
            'product' => $additional->name,
            'quantity' => $data['number'],
            'price' => $additional->price,
            'total' => $additional->price*$data['number'],
        );
    }
    if (isset($storage)) {
        $daffny->tpl->products[] = array(
            'item' => $storage->code,
            'product' => $storage->name,
            'quantity' => 1,
            'price' => $storage->price,
            'total' => $storage->price,
        );
    }

    if (isset($addon_aq)) {
        $daffny->tpl->products[] = array(
            'item' => $addon_aq->code,
            'product' => $addon_aq->name,
            'quantity' => 1,
            'price' => $addon_aq->price,
            'total' => $addon_aq->price,
        );
    }
    return $daffny->tpl->build('registration.invoice', $data);
}

$systemPhone = $daffny->DB->selectField('value', 'settings', "WHERE `name` = 'phone'");
$infoEmail = $daffny->DB->selectField('value', 'settings', "WHERE `name` = 'info_email'");

//frozen logic
//charge
$mm = new MembersManager($daffny->DB);

$members = $mm->getMembers("`id` = `parent_id` AND `status` = 'Active' AND chmod <> 1 " . ($is_manually ? " AND id = '" . getParentId() . "'" : ""));

//get all license owners
foreach ($members as $member) { //
    echo 'Member: ' . $member->id . '----------------------------------------<br/>';
    $profile = $member->getCompanyProfile();
    $settings = $member->getDefaultSettings();

    $currentlicense = new License($daffny->DB);
    if (!$profile->is_frozen) {
        if ($currentlicense->loadCurrentLicenseByMemberId($member->id)) { //load current license
            if (strtotime($currentlicense->expire) < time()) { // License is still active?
                if ($settings->billing_autopay) { //if billing autopay

                    $renewalArr = array(
                        'member_id' => $member->id,
                        'first_name' => $member->contactname,
                        'last_name' => "",
                        'company' => $profile->companyname,
                        'status' => Orders::STATUS_PENDING,
                        'address' => "",
                        'city' => "",
                        'state' => "",
                        'zip' => "",
                        'card_type_id' => "",
                        'card_first_name' => "",
                        'card_last_name' => "",
                        'card_number' => "",
                        'card_expire' => "",
                        'card_cvv2' => "",
                    );
                    printt($renewalArr);
                    echo "<br/>";

                    $product = new Product($daffny->DB);
                    $product->load($currentlicense->renewal_product_id);
                    $renewalorder = new Orders($daffny->DB);
                    $renewalArr['amount'] = $product->price;
                    if ($currentlicense->renewal_users > 0) {
                        $additionalId = $daffny->DB->selectField('id', Product::TABLE, "WHERE `is_online` = 1 AND `is_delete` = 0 AND `type_id` = " . Product::TYPE_ADDITIONAL . " AND `period_id` = '" . (int)$product->period_id . "'");
                        if ($additionalId) {
                            $additional = new Product($daffny->DB);
                            $additional->load($additionalId);
                            $renewalArr['amount'] += (float)$additional->price * (int)$currentlicense->renewal_users;
                        } else {
                            echo "Additional Product not found.<br />";
                            // WTF? Each Initial License should have additional license
                        }
                    }

                    if ($currentlicense->renewal_storage_id > 0) {
                        $storage = new Product($daffny->DB);
                        $storage->load($currentlicense->renewal_storage_id);
                        $renewalArr['amount'] += (float)$storage->price;
                    }

                    if ($currentlicense->renewal_addon_aq_id > 0) {
                        $addon_aq = new Product($daffny->DB);
                        $addon_aq->load($currentlicense->renewal_addon_aq_id);
                        $renewalArr['amount'] += (float)$addon_aq->price;
                    }


                    // START TRANSACTION
                    $licenseRenewed = false;

                    $daffny->DB->transaction('start');

                    $bm = new BillingManager($daffny->DB);
                    $bm->owner_id = $member->id;
                    $balance = $bm->getCurrentBalance();

                    echo "Balance: " . $balance . "<br/>";

                    // charge payment
                    $billing = new Billing($daffny->DB);
                    $billingArr = array(
                        'type' => Billing::TYPE_CHARGE,
                        'owner_id' => $member->id,
                        'added' => date('Y-m-d H:i:s'),
                        'amount' => $renewalArr['amount'],
                        'description' => 'License Renewal (from Balance)',
                        'transaction_id' => ''
                    );
                    $billing->create($billingArr);

                    //get from CC amount
                    $chargefromcc = 0;
                    if ($balance >= $renewalArr['amount']) {
                        $chargefromcc = 0;
                    } else {
                        if ($balance > 0) {
                            $chargefromcc = $renewalArr['amount'] - $balance;
                        } else {
                            $chargefromcc = $renewalArr['amount'];
                        }
                    }
                    echo "Charge from CC : " . $chargefromcc . "<br />";

                    if ($chargefromcc > 0) { //charge from CC
                        echo "load CC...<br />";
                        $creditcard = new Creditcard($daffny->DB);
                        $creditcard->key = $daffny->cfg['security_salt'];
                        if ($creditcard->getCurrentAutopayCC($settings->billing_cc_id, $member->id)) {
                            echo "CC found...<br />";
                            //update rest order fields
                            $renewalArr['first_name'] = $creditcard->cc_fname;
                            $renewalArr['last_name'] = $creditcard->cc_lname;
                            $renewalArr['address'] = $creditcard->cc_address;
                            $renewalArr['city'] = $creditcard->cc_city;
                            $renewalArr['state'] = $creditcard->cc_state;
                            $renewalArr['zip'] = $creditcard->cc_zip;
                            $renewalArr['card_type_id'] = $creditcard->cc_type;
                            $renewalArr['card_first_name'] = trim($creditcard->cc_fname);
                            $renewalArr['card_last_name'] = trim($creditcard->cc_lname);
                            $renewalArr['card_number'] = $creditcard->cc_number;
                            $renewalArr['card_expire'] = $creditcard->cc_month . substr($creditcard->cc_year, -2);
                            $renewalArr['card_cvv2'] = $creditcard->cc_cvv2;

                            $billing = new Billing($daffny->DB);
                            $billingArr = array(
                                'type' => Billing::TYPE_PAYMENT,
                                'owner_id' => $member->id,
                                'added' => date('Y-m-d H:i:s'),
                                'amount' => $chargefromcc,
                                'description' => 'Renewal Payment (from CC)',
                                'transaction_id' => ''
                            );
                            printt($billingArr);
                            echo "<br/>";
                            printt($renewalArr);
                            echo "<br/>";
                            $billing->create($billingArr);
                            $renewalorder->create($renewalArr);
                            if ($renewalorder->processAuthorize()) {
                                echo "Renewed<br/>";
                                $licenseRenewed = true;
                                $billing->update(array('transaction_id' => $renewalorder->transaction_id));
                                $daffny->DB->transaction('commit');
                            } else {
                                echo "Response: " . $renewalorder->response;
                                echo "Rollback";
                                // ROLLBACK TRANSACTION
                                $daffny->DB->transaction('rollback');
                                // TODO: Send renewal_failed email
                                $tplData = array(
                                    'first_name' => $renewalorder->first_name,
                                    'last_name' => $renewalorder->last_name,
                                    'amount' => $renewalorder->amount,
                                    'card_number' => "**** **** **** " . substr($renewalorder->card_number, -4),
                                    'expire' => date('m/d/Y'),
                                    'system_phone' => $systemPhone,
                                    'info_email' => $infoEmail,
                                );
                                $action->sendEmail($member->contactname, $member->email, "Your Freight Dragon license has not been renewed", "renewal_failed", $tplData);
                            }
                        } else { //load current CC
                            //check if we should froze account
                            echo "CC not found...";
                            if (strtotime($currentlicense->expire) < (time() - (3 * 86400))) {
                                $profile->update(array(
                                    'is_frozen' => 1,
                                ));
                                echo "Profile has been frozed.<br />";
                            }
                        }
                    } else {
                        $renewalArr['status'] = Orders::STATUS_PROCESSED;
                        $renewalorder->create($renewalArr);
                        $licenseRenewed = true;
                        $daffny->DB->transaction('commit');
                    }

                    if ($licenseRenewed) {
                        echo "Prepare new license<br />";
                        $daffny->DB->insert('orders_details', array(
                                'order_id' => $renewalorder->id,
                                'product_id' => $product->id,
                                'quantity' => 1,
                                'price' => $product->price,
                                'total' => $product->price*1
                            )
                        );

                        if (isset($additional)) {
                            $daffny->DB->insert('orders_details', array('order_id' => $renewalorder->id, 'product_id' => $additional->id, 'quantity' => $currentlicense->renewal_users, 'price' => $additional->price, 'total' => $additional->price * $currentlicense->renewal_users));
                        }
                        if (isset($storage)) {
                            $daffny->DB->insert('orders_details', array('order_id' => $renewalorder->id, 'product_id' => $storage->id, 'quantity' => 1, 'price' => $storage->price, 'total' => $storage->price * 1));
                        }
                        if (isset($addon_aq)) {
                            $daffny->DB->insert('orders_details', array('order_id' => $renewalorder->id, 'product_id' => $addon_aq->id, 'quantity' => 1, 'price' => $addon_aq->price, 'total' => $addon_aq->price * 1));
                        }


                        echo "Create new license<br />";
                        $newlicense = new License($daffny->DB);
                        $expire = new DateTime();
                        $expire->add(new DateInterval('P1' . ($product->period_id == Product::PERIOD_MONTH ? 'M' : 'Y')));

                        $new_license_arr = array(
                            'owner_id' => $member->id,
                            'order_id' => $renewalorder->id,
                            'users' => $currentlicense->renewal_users,
                            'expire' => $expire->format('Y-m-d'),
                            'period_type' => $product->period_id,
                            'product_id' => $product->id,
                            'storage_id' => isset($storage) ? $storage->id : 'NULL',
                            'addon_aq_id' => isset($addon_aq) ? $addon_aq->id : 'NULL',
                            'renewal_product_id' => $currentlicense->renewal_product_id,
                            'renewal_storage_id' => $currentlicense->renewal_storage_id,
                            'renewal_addon_aq_id' => $currentlicense->renewal_addon_aq_id,
                            'renewal_users' => $currentlicense->renewal_users
                        );


                        printt($new_license_arr);
                        echo "<br/>";

                        $newlicense->create($new_license_arr);



                        $profile->update(array(
                            'is_frozen' => 0,
                        ));

                        //change user count
                        $member->getNextInactiveUsers($member->id, $currentlicense->renewal_users, true);

                        $invoiceData = array(
                            'product' => $product->id,
                            'number' => $newlicense->users,
                        );
                        if (isset($additional)) {
                            $invoiceData['additional'] = $additional->id;
                        }


                        if (isset($storage)) {
                            $invoiceData['storage'] = $storage->id;
                        }

                        if (isset($addon_aq)) {
                            $invoiceData['addon_aq'] = $addon_aq->id;
                        }



                        $tplData = array(
                            'first_name' => $renewalorder->first_name,
                            'last_name' => $renewalorder->last_name,
                            'expire' => date('m/d/Y', strtotime($newlicense->expire)),
                            'renewal_receipt' => generateInvoice($invoiceData, $daffny),
                            'system_phone' => $systemPhone,
                            'info_email' => $infoEmail,
                        );
                        $action->sendEmail($member->contactname, $member->email, "Your CargoFlare license has been renewed", "renewal_success", $tplData);
                        $currentlicense->load($newlicense->id);
                    }

                }
                //if billing autopay
                //check if we should froze account
                if (strtotime($currentlicense->expire) < (time() - (3 * 86400))) {
                    $profile->update(array(
                        'is_frozen' => 1,
                    ));
                    echo "Profile has been frozed<br />";
                }
            } else {
                echo "Current license is still active <br />";
            }
            //if license still active
        } else { // WTF? Each member should have at least one license
            echo "No Current license <br />";
            $profile->update(array(
                'is_frozen' => 1,
            ));
        }
        //if load current license
    } else {
        echo "Profile is Frozen <br/>";
    }
    //if proile !is_frozen
    // Check if account should be frozen;

} //foreach member
require_once("done.php");