<?php

namespace Hasirciogli\SessionWrapper\Storage;

use Hasirciogli\SessionWrapper\Interfaces\StorageInterface;
use Hasirciogli\Hdb\Interfaces\Database\Config\DatabaseConfigInterface;
use Hasirciogli\Hdb\Database;


class MysqlStorage implements StorageInterface
{
    /**
     * @param DatabaseConfigInterface $DatabaseConfig;
     */
    private DatabaseConfigInterface $DatabaseConfig;

    public function __construct(DatabaseConfigInterface $DatabaseConfig)
    {
        $this->DatabaseConfig = $DatabaseConfig;
    }

    public function Get($SessionId): array|null
    {
        if (!($result = Database::cfun($this->DatabaseConfig)->Select("sessions")->where("session_id", true)->BindParam("session_id", $SessionId)->run()->get()))
            return null;

        return (array)json_decode($result["data"], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    public function Set($SessionId, $Data): bool
    {
        if (!($result = Database::cfun($this->DatabaseConfig)->CustomSql("UPDATE sessions SET data=:data WHERE BINARY session_id=:session_id")->BindParam("session_id", $SessionId)->BindParam("data", json_encode($Data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->run()))
            return false;

        return true;
    }
    public function Check($SessionId): bool
    {
        if (!($result = Database::cfun($this->DatabaseConfig)->Select("sessions")->where("session_id", true)->BindParam("session_id", $SessionId)->run()->get()))
            return false;

        return true;
    }

    public function Add($SessionId, $Data = []): bool
    {
        $Result = $this->Check($SessionId);
        if (!$Result)
            if (!($result = Database::cfun($this->DatabaseConfig)->Insert("sessions", ["session_id"])->BindParam("session_id", $SessionId)->run()))
                return false;
            else
                return true;
        else
            return false;
    }

    public function Remove($SessionId): bool
    {
        $Result = $this->Check($SessionId);
        if ($Result)
            if (!($result = Database::cfun($this->DatabaseConfig)->CustomSql("DELETE FROM sessions WHERE BINARY session_id=:session_id")->BindParam("session_id", $SessionId)->run()))
                return false;
            else
                return true;
        else
            return false;
    }
}