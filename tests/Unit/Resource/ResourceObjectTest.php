<?php

/** @var \LabelcampAPI\Resource Tests\TestCase */

use LabelcampAPI\Resource\ResourceObject;

it('can be initiated with only type', function () {
    $api = new ResourceObject("type");

    expect($api)->toBeInstanceOf(ResourceObject::class);
});

it('can be initiated with type and id', function () {
    $api = new ResourceObject("type", "id");

    expect($api)->toBeInstanceOf(ResourceObject::class);
});
