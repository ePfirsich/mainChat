<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that login page works');
$I->amOnPage('/');
$I->switchToIFrame('topframe');
$I->see('Login');