<?php
namespace App\Tests;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

   /**
    * Define custom actions here
    */

    public function grabResponseAsArray()
    {
        return json_decode($this->grabResponse(), true);
    }

    public function amAuthenticatedAsUser1()
    {
        // Ten years token
        $this->amBearerAuthenticated('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NzIzMzkxNDQsImV4cCI6MTg4NzY5OTE0NCwiaWQiOjEsImdyYW50cyI6WyJhbGwiXX0.R4fzi67Q-Jtr7x7Zv_qJp4_zyZXgQfl6CfR-_85qLNK0ZzMAtWwRThTqct3lNug7hsX8as5hTjdM99RPPjkzhl4NIb5CnCWI-6mCwmpyrJ8VkK5GwrvqMgldTsvaYOPOYig8JWkx74NDmwJ_cWJ4bcq12w5uuVinr4BzxaC1I_nWnna0EFejuUnwm2PFQdZJRjwBZKFvErzUJNt1BiMjUrJvO2uX1fpxGpXa_pxmu4-93MfmjNQYw7PmstIRr0dZSfroWrFQ1bAWu0ap8nf4eqQnoapue_rDQU_VN8Lol2U93srWGQknYOXbpzLBZBnJgyUK5-BLttDQ7L-s9vDRTZbxsXv3gj3Xd2-lTsAN0pQonVAYnX3GPoQn--QCE2ASykL0NDKtthE6LMeKit6c1-omtmXf2LnbUC4JsPaaVCjcCUh9dV6n5eOb6sFO6sEmYYd-eJhu73ixyXt12ekzLOyhQyOKnrdwgX22kR2fxBKffnxXtEpYfQ86PKy1H0mNrP2nLjKancGF9IuMnZBteM_eNQeYRMD72gzAaR5rhgASQRZiqbWDBgUerFSNG7_hWWxJSF-JsntHHYBObcYJ59ZmLxh1oeM5TK6DN_vKvS4bXl7cez0p_2Y-qkq0umProhJcXf1i9tdKuHRu3hiLH3v7mySWPBcaHJmcbc6z9K0');
    }
}
