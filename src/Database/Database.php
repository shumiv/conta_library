<?php

namespace conta\Database;

class Database
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function execute(string $queryString, array $params = []): array
    {
        $query = $this->pdo->prepare($queryString);
        $this->executeQuery($query, $params);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function executeQuery($query, array $params): void
    {
        if ($query->execute($params) === false) {
            $error_message = print_r($query->errorInfo(), true);
            throw new \Exception("SQL error with inserting in Entity-table. PDO errorInfo is: "
                . $error_message . ". Query string was: '"
                . $query->queryString . "' Parameters was: " . print_r($params, true));
        }
    }
}
