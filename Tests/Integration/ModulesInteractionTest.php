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
 * Date: 06.05.19
 * Time: 08:34
 */

namespace OxidAcademy\OxCoin\Tests\Integration;

use OxidEsales\Eshop\Application\Controller\PaymentController;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Delivery;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

class ModulesInteractionTest extends UnitTestCase
{

    public function setUp(): void {
        parent::setUp();
        
        $this->addToDatabase("REPLACE INTO `oxpayments` (`OXID`, `OXACTIVE`, `OXDESC`, `OXADDSUM`, `OXADDSUMTYPE`, `OXADDSUMRULES`, `OXFROMBONI`, `OXFROMAMOUNT`, `OXTOAMOUNT`, `OXVALDESC`, `OXCHECKED`, `OXDESC_1`, `OXVALDESC_1`, `OXDESC_2`, `OXVALDESC_2`, `OXDESC_3`, `OXVALDESC_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXSORT`, `OXTIMESTAMP`) VALUES 
        ('oxcoin',1,'Coin',0,'abs',0,0,0,1000000,'',0,'Coin','','','','','','','','','',0,'2021-05-19 16:30:00');", "oxpayments");

        $this->addToDatabase("REPLACE INTO `oxpayments` (`OXID`, `OXACTIVE`, `OXDESC`, `OXADDSUM`, `OXADDSUMTYPE`, `OXADDSUMRULES`, `OXFROMBONI`, `OXFROMAMOUNT`, `OXTOAMOUNT`, `OXVALDESC`, `OXCHECKED`, `OXDESC_1`, `OXVALDESC_1`, `OXDESC_2`, `OXVALDESC_2`, `OXDESC_3`, `OXVALDESC_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXSORT`, `OXTIMESTAMP`) VALUES 
        ('test_payment',1,'Test',0,'abs',0,0,0,1000000,'',0,'Test','','','','','','','','','',0,'2021-05-19 16:30:00');", "oxpayments");

    }

    public function testPaymentListProvidedByThePaymentController()
    {
        $_POST['sShipSet'] = 'oxidstandard';

        // <Creating Shipping Cost Rules>
        $delivery = oxNew(Delivery::class);
        $delivery->setId('_delivery_test_id');
        $delivery->oxdelivery__oxactive = new Field(1);
        $delivery->oxdelivery__oxparamend = new Field(999);
        $delivery->save();

        $del2delset = oxNew(BaseModel::class);
        $del2delset->init('oxdel2delset');
        $del2delset->oxdel2delset__oxdelid = new Field($delivery->getId());
        $del2delset->oxdel2delset__oxdelsetid = new Field('oxidstandard');
        $del2delset->save();
        // </Creating Shipping Cost Rules>


        // Assigning the payment to the shipping method oxidstandard.
        $delset2payment = oxNew(BaseModel::class);
        $delset2payment->init('oxobject2payment');
        $delset2payment->oxobject2payment__oxpaymentid = new Field('oxcoin');
        $delset2payment->oxobject2payment__oxobjectid = new Field('oxidstandard');
        $delset2payment->oxobject2payment__oxtype = new Field('oxdelset');
        $delset2payment->save();


        // <Creating a basket>
        $price = oxNew(Price::class);
        $price->setPrice(1.0);

        $article = oxNew(Article::class);
        $article->setId('test_665');
        $article->setPrice($price);
        $article->save();

        $basket = oxNew(Basket::class);
        $basket->addToBasket($article->getId(), 1.0);

        Registry::getSession()->setBasket($basket);
        // </Creating a basket>


        // Creating a user
        $user = oxNew(User::class);
        $user->setId('_user_test_id');
        $user->oxusers__oxusername = new Field('_user_test_username');
        $user->save();


        // Creating a PaymentController object and inject the user object.
        $controller = oxNew(PaymentController::class);
        $controller->setUser($user);

        // From all payments at least our payment method oxcoin must be in the list.
        $ids = [];
        foreach ($controller->getPaymentList() as $payment) {
            $ids[] = $payment->oxpayments__oxid->value;
        }

        $this->assertContains('oxcoin', $ids);
    }
}
