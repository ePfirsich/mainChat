<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that login page works');
$I->amOnPage('/');
$I->see('Login');