<?php

namespace App\Service;

use MongoDB\Client;

class MongoService
{
    private Client $client;

    public function __construct(string $uri)
    {
        $this->client = new Client($uri);
    }

    /**
     * Met à jour ou crée le document des stats quotidiennes.
     */
    public function upsertDailyStats(string $collection, string $date, int $nbCouvert): void
    {
        $dbCollection = $this->client->selectDatabase('quai_antique')
                                     ->selectCollection($collection);

        // Crée le document s'il n'existe pas
        $doc = $dbCollection->findOne(['date' => $date]);
        if (!$doc) {
            $dbCollection->insertOne([
                'date' => $date,
                'totalReservations' => 0,
                'totalCouverts' => 0
            ]);
        }

        // Incrémente les compteurs
        $dbCollection->updateOne(
            ['date' => $date],
            ['$inc' => [
                'totalReservations' => 1,
                'totalCouverts' => $nbCouvert
            ]]
        );
    }

    /**
     * Retourne les stats quotidiennes pour la date donnée.
     */
    public function getDailyStats(string $collection, string $date): array
    {
        $doc = $this->client->selectDatabase('quai_antique')
                            ->selectCollection($collection)
                            ->findOne(['date' => $date]);

        return $doc ? $doc->getArrayCopy() : [
            'date' => $date,
            'totalReservations' => 0,
            'totalCouverts' => 0
        ];
    }
}