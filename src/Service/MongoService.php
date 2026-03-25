<?php

namespace App\Service;

use MongoDB\Client;
use MongoDB\UpdateResult;

class MongoService
{
    private Client $client;

    public function __construct(string $uri)
    {
        $this->client = new Client($uri);
    }

    public function insertOne(string $collection, array $document): void
    {
        $this->client->selectDatabase('quai_antique')
                     ->selectCollection($collection)
                     ->insertOne($document);
    }

    public function upsertDailyStats(string $collection, string $date, int $nbCouvert): UpdateResult
    {
        $dbCollection = $this->client->selectDatabase('quai_antique')
                                     ->selectCollection($collection);

        return $dbCollection->updateOne(
            ['date' => $date], // critère : date
            [
                '$inc' => [               // incrémente les valeurs existantes
                    'totalReservations' => 1,
                    'totalCouverts' => $nbCouvert
                ]
            ],
            ['upsert' => true] // crée le document si il n'existe pas
        );
    }

    public function getDailyStats(string $collection, string $date): array
    {
        $doc = $this->client->selectDatabase('quai_antique')
                            ->selectCollection($collection)
                            ->findOne(['date' => $date]);

        return $doc ? $doc->getArrayCopy() : ['date' => $date, 'totalReservations' => 0, 'totalCouverts' => 0];
    }
}