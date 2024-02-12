<?php

/** @var \LabelcampAPI\Resource Tests\TestCase */

use LabelcampAPI\Resource\ToManyRelationship;

it('can be used with single relationship', function () {
    $api = new ToManyRelationship();
    $api->addResourceIdentifier("a", "1");

    $api_expect = [
        "data" => [
            [
                "type" => "a",
                "id" => "1",
            ],
        ],
    ];

    expect($api_expect)->toBe($api->toArray());
});

it('can be used with many relationship', function () {
    $api = new ToManyRelationship();
    $api->addResourceIdentifier("a", "1");
    $api->addResourceIdentifier("b", "2");

    $api_expect = [
        "data" => [
            [
                "type" => "a",
                "id" => "1",
            ],
            [
                "type" => "b",
                "id" => "2",
            ]
        ],
    ];

    expect($api_expect)->toBe($api->toArray());
});
