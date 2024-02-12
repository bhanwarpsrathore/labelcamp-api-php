<?php

declare(strict_types=1);

namespace LabelcampAPI\Resource;

final class ToManyRelationship {

    private array $resourceIdentifiers  = [];

    public function addResourceIdentifier(string $type, string $id): self {
        $resourceIdentifier = [
            "type" => $type,
            "id" => $id
        ];

        $this->resourceIdentifiers[] = $resourceIdentifier;

        return $this;
    }

    public function toArray(): array {
        return [
            "data" => $this->resourceIdentifiers
        ];
    }
}
