<?php

namespace Hasirciogli\SessionWrapper\Interfaces;

interface StorageInterface
{
    public function Get($SessionId): array|null;
    public function Set($SessionId, $Data): bool;
    public function Check($SessionId): bool;
    public function Add($SessionId, $Data): bool;
    public function Remove($SessionId): bool;
}