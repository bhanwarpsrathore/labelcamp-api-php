<?php

use LabelcampAPI\Resource\ToOneRelationship;

it('can be initiated with type and id', function () {
    $api = new ToOneRelationship("type", "id");

    expect($api)->toBeInstanceOf(ToOneRelationship::class);
});

it('can be used with single relationship', function () {
    $api = new ToOneRelationship("a", "1");

    $api_expect = [
        "data" =>
        [
            "type" => "a",
            "id" => "1"
        ]
    ];

    expect($api_expect)->toBe($api->toArray());
});
