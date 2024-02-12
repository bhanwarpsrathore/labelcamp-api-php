<?php

declare(strict_types=1);

namespace LabelcampAPI\Resource;

class ResourceObject {

    /**
     * Resource Id
     * @var string
     */
    private string $id;

    /**
     * Resource Type
     * @var string
     */
    private string $type;

    /**
     * Resource Attribute
     * @var array <string, mixed>
     */
    private array $attributes;

    /**
     * Resource Relationships
     * @var array <string, RelationshipInterface>
     */
    private array $relationships;

    public function __construct(string $type, string $id = '') {
        $this->type = $type;
        $this->id = $id;
        $this->attributes = [];
        $this->relationships = [];
    }

    /**
     * Set Attribute
     * 
     * @param string $name
     * @param string|array|mixed $value
     */

    public function setAttributes(string $name, $value): self {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function setRelationship(string $name, $relationship): self {
        $this->relationships[$name] = $relationship;

        return $this;
    }

    public function toArray(): array {
        $resource = [
            "data" => [
                "type" => $this->type
            ]
        ];

        if ($this->id !== '') {
            $resource['data']['id'] = $this->id;
        }

        if (empty($this->attributes) == false) {
            $resource['data']['attributes'] = $this->attributes;
        }

        foreach ($this->relationships as $name => $relationship) {
            $resource['data']['relationships'][$name] = $relationship->toArray();
        }

        return $resource;
    }
}
