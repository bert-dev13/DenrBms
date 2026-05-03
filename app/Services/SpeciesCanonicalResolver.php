<?php

namespace App\Services;

use App\Models\Species;

final class SpeciesCanonicalResolver
{
    /**
     * Known taxonomy/data-entry aliases mapped to canonical normalized scientific names.
     *
     * @var array<string, string>
     */
    private array $scientificAliases = [
        // Common typo observed in field records; treat as Shorea contorta.
        'shorea concorta' => 'shorea contorta',
    ];

    /** @var array<int, Species>|null */
    private ?array $speciesRows = null;

    /** @var array<int, Species> */
    private array $byId = [];

    /** @var array<string, Species> */
    private array $byScientific = [];

    /** @var array<string, Species> */
    private array $byScientificBinomial = [];

    /** @var array<string, Species> */
    private array $byCommon = [];

    /**
     * @return array{
     *   key: string,
     *   species_id: int|null,
     *   common_name: string,
     *   scientific_name: string,
     *   species: Species|null
     * }
     */
    public function resolve(string $scientificName, string $commonName, ?int $speciesId = null): array
    {
        $this->ensureLoaded();

        $scientificName = trim($scientificName);
        $commonName = trim($commonName);
        $normScientific = $this->normalize($scientificName);
        $normScientific = $this->applyScientificAlias($normScientific);
        $normCommon = $this->normalize($commonName);

        $species = null;
        if ($speciesId !== null && $speciesId > 0 && isset($this->byId[$speciesId])) {
            $species = $this->byId[$speciesId];
        } elseif ($normScientific !== '' && isset($this->byScientific[$normScientific])) {
            $species = $this->byScientific[$normScientific];
        } elseif ($normScientific !== '') {
            $binomial = $this->toBinomialKey($normScientific);
            if ($binomial !== '' && isset($this->byScientificBinomial[$binomial])) {
                $species = $this->byScientificBinomial[$binomial];
            }
        }

        if ($species === null && $normCommon !== '' && isset($this->byCommon[$normCommon])) {
            $species = $this->byCommon[$normCommon];
        }

        if ($species === null && $normScientific !== '') {
            $species = $this->findClosestBinomialSpecies($normScientific);
        }

        if ($species !== null) {
            $species = $this->resolveCanonicalSpeciesAlias($species);
            $species = $this->collapseNearDuplicateByCommonName($species, $normScientific, $normCommon);

            return [
                'key' => 'id:'.(int) $species->id,
                'species_id' => (int) $species->id,
                'common_name' => trim((string) $species->name) !== '' ? (string) $species->name : ($commonName !== '' ? $commonName : 'Unspecified'),
                'scientific_name' => trim((string) $species->scientific_name) !== '' ? (string) $species->scientific_name : $scientificName,
                'species' => $species,
            ];
        }

        $fallback = $normScientific !== '' ? 's:'.$normScientific : ($normCommon !== '' ? 'c:'.$normCommon : 'unspecified');
        return [
            'key' => 'raw:'.$fallback,
            'species_id' => null,
            'common_name' => $commonName !== '' ? $commonName : 'Unspecified',
            'scientific_name' => $scientificName,
            'species' => null,
        ];
    }

    private function ensureLoaded(): void
    {
        if ($this->speciesRows !== null) {
            return;
        }

        $this->speciesRows = Species::query()
            ->orderBy('id')
            ->get(['id', 'name', 'scientific_name', 'is_endemic', 'is_migratory', 'conservation_status'])
            ->all();

        foreach ($this->speciesRows as $species) {
            $id = (int) $species->id;
            if ($id > 0 && ! isset($this->byId[$id])) {
                $this->byId[$id] = $species;
            }

            $scientific = $this->normalize((string) $species->scientific_name);
            if ($scientific !== '' && ! isset($this->byScientific[$scientific])) {
                $this->byScientific[$scientific] = $species;
            }

            $binomial = $this->toBinomialKey($scientific);
            if ($binomial !== '' && ! isset($this->byScientificBinomial[$binomial])) {
                $this->byScientificBinomial[$binomial] = $species;
            }

            $common = $this->normalize((string) $species->name);
            if ($common !== '' && ! isset($this->byCommon[$common])) {
                $this->byCommon[$common] = $species;
            }
        }
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value));
        if ($value === '') {
            return '';
        }

        $value = preg_replace('/[\p{P}\p{S}]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    private function toBinomialKey(string $normalizedScientific): string
    {
        if ($normalizedScientific === '') {
            return '';
        }
        $parts = array_values(array_filter(explode(' ', $normalizedScientific), static fn (string $p): bool => $p !== ''));
        if (count($parts) < 2) {
            return $normalizedScientific;
        }

        return $parts[0].' '.$parts[1];
    }

    private function applyScientificAlias(string $normalizedScientific): string
    {
        if ($normalizedScientific === '') {
            return '';
        }

        return $this->scientificAliases[$normalizedScientific] ?? $normalizedScientific;
    }

    private function resolveCanonicalSpeciesAlias(Species $species): Species
    {
        $normalized = $this->normalize((string) $species->scientific_name);
        $canonical = $this->applyScientificAlias($normalized);
        if ($canonical === $normalized) {
            return $species;
        }

        return $this->byScientific[$canonical] ?? $species;
    }

    private function collapseNearDuplicateByCommonName(Species $species, string $normScientific, string $normCommon): Species
    {
        if ($normCommon === '' || ! isset($this->byCommon[$normCommon])) {
            return $species;
        }

        $commonMatched = $this->byCommon[$normCommon];
        $speciesScientific = $this->normalize((string) $species->scientific_name);
        $commonScientific = $this->normalize((string) $commonMatched->scientific_name);

        $left = $this->toBinomialKey($speciesScientific !== '' ? $speciesScientific : $normScientific);
        $right = $this->toBinomialKey($commonScientific);
        if ($left === '' || $right === '' || ! $this->isNearSameBinomial($left, $right)) {
            return $species;
        }

        return ((int) $commonMatched->id) < ((int) $species->id) ? $commonMatched : $species;
    }

    private function isNearSameBinomial(string $left, string $right): bool
    {
        $a = explode(' ', $left);
        $b = explode(' ', $right);
        if (count($a) < 2 || count($b) < 2 || $a[0] !== $b[0]) {
            return false;
        }

        return levenshtein($a[1], $b[1]) <= 2;
    }

    private function findClosestBinomialSpecies(string $normalizedScientific): ?Species
    {
        $parts = array_values(array_filter(explode(' ', $normalizedScientific), static fn (string $p): bool => $p !== ''));
        if (count($parts) < 2) {
            return null;
        }

        $genus = $parts[0];
        $epithet = $parts[1];
        $best = null;
        $bestDistance = PHP_INT_MAX;

        foreach ($this->byScientificBinomial as $binomial => $species) {
            $candidate = explode(' ', $binomial);
            if (count($candidate) < 2 || $candidate[0] !== $genus) {
                continue;
            }

            $distance = levenshtein($epithet, $candidate[1]);
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $species;
            }
        }

        return $bestDistance <= 2 ? $best : null;
    }
}
