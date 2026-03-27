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

    public function insertReservation(string $collection, string $date, string $heure, int $nbCouvert): void
    {
        $this->client->selectDatabase('quai_antique')
            ->selectCollection($collection)
            ->insertOne([
                'date' => $date,
                'heure' => $heure,
                'nbCouvert' => $nbCouvert
            ]);
    }

    public function getReservationsByDate(string $date): array
    {
        $cursor = $this->client->selectDatabase('quai_antique')
            ->selectCollection('reservations')
            ->find(['date' => $date]);

        $totals = [];

        foreach ($cursor as $res) {
            $heure = $res['heure'];
            $totals[$heure] = ($totals[$heure] ?? 0) + $res['nbCouvert'];
        }

        return $totals;
    }

    public function getTotalCouvertsForHeure(string $date, string $heure): int
    {
        $cursor = $this->client->selectDatabase('quai_antique')
            ->selectCollection('reservations')
            ->find([
                'date' => $date,
                'heure' => $heure
            ]);

        $total = 0;

        foreach ($cursor as $res) {
            $total += $res['nbCouvert'];
        }

        return $total;
    }

    public function getSlotsByDate(string $date): array
    {
        $collection = $this->client->selectDatabase('quai_antique')->selectCollection('reservations');

        $slots = [];
        $docs = $collection->find(['date' => $date]);
        foreach ($docs as $doc) {
            $heure = $doc['heure'];
            $slots[$heure] = ($slots[$heure] ?? 0) + $doc['nbCouvert'];
        }

        return $slots;
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