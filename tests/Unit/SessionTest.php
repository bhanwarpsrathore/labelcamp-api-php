<?php

/** @var \LabelcampAPI\Tests\TestCase */

use LabelcampAPI\Request;
use LabelcampAPI\Session;

it('can be initiated without username and password', function () {
    $session = new Session();

    expect($session)->toBeInstanceOf(Session::class);
});

it('can be initiated with username and password', function () {
    $session = new Session('username', 'password');

    expect($session)->toBeInstanceOf(Session::class);
});

it('can be initiated with a request object', function () {
    $request = new Request();
    $session = new Session('username', 'password', $request);

    expect($session)->toBeInstanceOf(Session::class);
});
