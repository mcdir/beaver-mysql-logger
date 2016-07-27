<?php

namespace models;

use KZ\app\interfaces as appInterfaces;

class ExplainQuery extends ExecDbQuery
{
    protected function runQuery()
    {
        if (!$this->generalLogModel->isAllowExplain($this->commandType, $this->sql))
            throw new \RuntimeException('This query is not allowed to explain!');

        $stmt = $this->connection->prepare('explain ' . $this->sql);
        $stmt->execute();

        $out = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $out;
    }
} 
