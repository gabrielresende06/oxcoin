<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @author        OXID Academy
 * @link          https://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2019
 *
 * User: michael
 * Date: 22.05.19
 * Time: 08:19
 */

namespace OxidAcademy\OxCoin\Model;

use OxidEsales\EshopCommunity\Application\Model\Payment;

/**
 * Class Order
 * @package OxidAcademy\OxCoin\Application\Model
 */
class PaymentList extends PaymentList_parent
{

    public function getPaymentList($shipSetId, $price, $user = null) {
        $paymentList = parent::getPaymentList($shipSetId, $price, $user);

        $oxCoin = oxNew(Payment::class);
        $paymentList['oxcoin'] = $paymentList;
        
        return $paymentList;
    }
   
    public function doNothing()
    {
        return null;
    }
}
