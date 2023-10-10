<?php

namespace Hasirciogli\SessionWrapper;

use Hasirciogli\Hdb\Interfaces\Database\Config\DatabaseConfigInterface;
use Hasirciogli\SessionWrapper\Interfaces\StorageInterface;
use Hasirciogli\SessionWrapper\Storage\MysqlStorage;


class Session
{
    private StorageInterface $Storage;
    private static $SessionId = null;
    public function __construct(DatabaseConfigInterface $DatabaseConfig, StorageInterface $Storage = null)
    {
        if ($Storage != null)
            $this->Storage = $Storage;
        else {
            $this->Storage = new MysqlStorage($DatabaseConfig);
        }

        if (!self::$SessionId) {
            $this->GetSessionIdFromRequestedHeaders();
        }
    }

    private function GetSessionIdFromRequestedHeaders(): void
    {
        $SessionId = $_COOKIE["HOGLISESSID"] ?? "-1";

        if (!$this->Storage->Check($SessionId))
            $this->RegenSessionId();
        else {
            if (!$SessionId)
                $this->RegenSessionId();
            else
                self::$SessionId = $SessionId;
        }
    }
    private function RegenSessionId()
    {
        $SessionId = sha1(time() . time() . time() . time() . time());

        while ($this->Storage->Check($SessionId)) {
            $SessionId = sha1(time() . time() . time() . time() . time());
        }

        if (!$this->Storage->Add($SessionId, []))
            throw new \ErrorException("New session id assignation failed");

        self::$SessionId = $SessionId;
        setcookie("HOGLISESSID", $SessionId, time() + (86400 * 30), "/");
    }
    public function Get($Key): mixed
    {
        if (!$this->Storage->Check(self::$SessionId))
            $this->RegenSessionId();

        $Data = $this->Storage->Get(self::$SessionId);

        if ($Data === null)
            return null;

        return $Data[$Key];
    }
    public function Set($Key, $Value): bool
    {
        if (!$this->Storage->Check(self::$SessionId))
            $this->RegenSessionId();

        $Data = $this->Storage->Get(self::$SessionId);

        if ($Data === null)
            return false;


        $Data[$Key] = $Value;

        return $this->Storage->Set(self::$SessionId, $Data);
    }
}