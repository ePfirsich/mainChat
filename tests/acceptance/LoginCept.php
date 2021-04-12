<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('log in');
$I->amOnPage('/');
$I->fillField('login', 'admin');
$I->fillField('passwort', 'admin');
$I->executeJS('parent.setTimeout(window.stop, 3000)');
$I->click('input[type=submit][value=Login]');
$I->seeElement('frame[name=chat]');