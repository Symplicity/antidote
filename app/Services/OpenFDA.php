<?php

namespace App\Services;

use Log;
use GuzzleHttp\Client;

class OpenFDA extends RestAPI
{
    protected $api_base_uri = 'https://api.fda.gov/';
    protected $rate_limit = 20;

    public function __construct(Client $client)
    {
        parent::__construct($client);

        if ($api_key = env('OPENFDA_API_KEY', false)) {
            $this->setOptions(['api_key' => $api_key]);
        } else {
            Log::error('OpenFDA api key is not set!');
        }
    }

    private function getRecords($type, $query, $page = 0)
    {
        $skip = $page * 100;
        $results = $this->get("drug/{$type}.json?search= " . $query . "&skip={$skip}&limit=100");

        return array_get($results, 'results', []);
    }

    private function getCounts($type, $query, $field)
    {
        $counts = [];

        $results = $this->get("drug/{$type}.json?search=" . $query . "&count={$field}.exact");
        foreach (array_get($results, 'results', []) as $result) {
            $counts[$result['term']] = $result['count'];
        }

        return $counts;
    }

    private function cleanTerm($term)
    {
        $words = [
            'DESCRIPTION',
            'PURPOSE',
            'INDICATIONS AND USAGE',
            'INDICATIONS & USAGE',
            'INDICATION AND USAGE',
            'Uses'
            ];

        foreach ($words as $word) {
            $pos = stripos($term, $word);
            $wlen = strlen($word);

            if ($pos !== false && $pos < $wlen) {
                $term = substr($term, $pos + $wlen + 1);
                break;
            }
        }

        return $term;
    }

    private function getDescription($record)
    {
        $description = $this->cleanTerm(array_get($record, 'indications_and_usage.0', ''));
        if (empty($description)) {
            $description = $this->cleanTerm(array_get($record, 'description.0', ''));
            if (empty($description)) {
                $description = $this->cleanTerm(array_get($record, 'purpose.0', ''));
            }
        }

        return $description;
    }

    public function getLabel($concept, $rxcuis)
    {
        $label = ['name' => '', 'description' => '', 'prescription_types' => []];

        $matching_label = [];
        $brand = array_get($concept, 'label', '');
        $generic = str_replace(' / ', ', ', array_get($concept, 'generic', ''));

        $query = 'openfda.rxcui:(' . join('+', $rxcuis) . ')';
        $page = 0;

        do {
            $records = $this->getRecords('label', $query, $page++);
            foreach ($records as $record) {
                $brand_name = array_get($record, 'openfda.brand_name.0');

                if ($brand && $brand_name && strtolower($brand) == strtolower($brand_name)) {
                    $label['name'] = $brand_name;
                    $label['description'] = $this->getDescription($record);
                } elseif (empty($matching_label)) {
                    $generic_name = array_get($record, 'openfda.generic_name.0');
                    $substances = array_get($record, 'openfda.substance_name', []);
                    sort($substances);
                    $substance_name = join(', ', $substances);

                    if ($generic && $generic_name && strtolower($generic) == strtolower($generic_name)) {
                        $matching_label['name'] = $generic_name;
                        $matching_label['description'] = $this->getDescription($record);
                    } elseif ($generic && $substance_name && strtolower($generic) == strtolower($substance_name)) {
                        $matching_label['name'] = $substance_name;
                        $matching_label['description'] = $this->getDescription($record);
                    }
                }

                if ($product_type = array_get($record, 'openfda.product_type.0')) {
                    if ($product_type == 'HUMAN PRESCRIPTION DRUG') {
                        $label['prescription_types']['1'] = true;
                    } elseif ($product_type == 'HUMAN OTC DRUG') {
                        $label['prescription_types']['2'] = true;
                    }
                }
            }
        } while (empty($label['description']) && count($label['prescription_types']) != 2 && $page < 5);

        if (empty($label['name']) && !empty($matching_label)) {
            $label['name'] = $matching_label['name'];
            $label['description'] = $matching_label['description'];
        }
        $label['prescription_types'] = array_keys($label['prescription_types']);

        return $label;
    }

    public function getRecalls($rxcuis)
    {
        $recalls = [];

        $query = 'openfda.rxcui:(' . join('+', $rxcuis) . ')+AND+status:Ongoing';

        $records = $this->getRecords('enforcement', $query);

        foreach ($records as $record) {
            $recalls[] = [
                'description' => $record['product_description'],
                'number' => $record['recall_number'],
                'date' => $record['recall_initiation_date'],
                'recall' => $record['reason_for_recall'],
                'lots' => $record['code_info'],
                ];
        }

        return $recalls;
    }

    public function getIndications($rxcuis)
    {
        $query = 'patient.drug.openfda.rxcui:(' . join('+', $rxcuis) . ')';

        $counts = $this->getCounts('event', $query, 'patient.drug.drugindication');

        return $counts;
    }

    public function getSideEffects($rxcuis)
    {
        $query = 'patient.drug.openfda.rxcui:(' . join('+', $rxcuis) . ')';

        $counts = $this->getCounts('event', $query, 'patient.reaction.reactionmeddrapt');

        return $counts;
    }
}
