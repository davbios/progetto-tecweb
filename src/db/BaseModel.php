<?php

class BaseModel
{
    protected ?int $id;
    protected ?DateTime $created_at;
    protected ?DateTime $updated_at;

    public function __construct(?int $id, ?DateTime $created_at, ?DateTime $updated_at)
    {
        $this->id = $id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }
}