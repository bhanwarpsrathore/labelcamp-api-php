<?php

/** @var \LabelcampAPI\Tests\TestCase */

use LabelcampAPI\LabelcampAPI;
use LabelcampAPI\Request;
use LabelcampAPI\Session;

it('can be initiated without options', function () {
    $api = new LabelcampAPI();

    expect($api)->toBeInstanceOf(LabelcampAPI::class);
});

it('can be initiated with options', function () {
    $api = new LabelcampAPI([
        'auto_refresh' => true,
        'auto_retry' => true
    ]);

    expect($api)->toBeInstanceOf(LabelcampAPI::class);
});

it('can be initiated with Session object', function () {
    $session = new Session('username', 'password');
    $api = new LabelcampAPI([], $session);

    expect($api)->toBeInstanceOf(LabelcampAPI::class);
});

it('can be initiated with Request object', function () {
    $request = new Request();

    $api = new LabelcampAPI([], null, $request);

    expect($api)->toBeInstanceOf(LabelcampAPI::class);
});
