<?php

declare(strict_types=1);

namespace LabelcampAPI\Resource;

final class ToOneRelationship {

    private string $type;
    private string $id;

    public function __construct(string $type = '', string $id = '') {
        $this->type = $type;
        $this->id = $id;
    }

    public function toArray(): array {
        $resourceIdentifier = [
            "type" => $this->type,
            "id" => $this->id
        ];

        return [
            "data" => $resourceIdentifier
        ];
    }
}
