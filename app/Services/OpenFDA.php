<?php

namespace App\Services;

use Log;
use GuzzleHttp\Client;

class OpenFDA extends RestAPI
{
    protected $api_base_uri = 'https://api.fda.gov/';
    protected $rate_limit = 4;

    public function __construct(Client $client)
    {
        parent::__construct($client);
        
        if ($api_key = env('OPENFDA_API_KEY', false)) {
            $this->setOptions(['api_key' => $api_key]);
        } else {
            Log::error('OpenFDA api key is not set!');
        }
    }
    
    public function sanitizeName($name)
    {
        if ($name && ($sanitized = preg_replace('/\+{2,}/', '+', preg_replace('/\W/', '+', $name)))) {
            return strtolower($sanitized);
        }
    }

    public function getDescription($concept, $records)
    {
        $description = '';

        $rxcui = $concept['rxcui'];
        $type = $concept['type'];
        $name = array_get($concept, 'sanitized_' . $type, '');

        if ($name) {
            foreach ($records as $record) {
                $desc = array_get($record, 'description.0', '');
                if (!$desc) {
                    $desc = array_get($record, 'purpose.0', '');
                }
                if ($desc) {
                    $rxcuis = array_get($record, 'openfda.rxcui', []);
                    $sanitized = array_get($record, 'sanitized_' . $type, '');

                    if (in_array($rxcui, $rxcuis) || ($sanitized && $name == $sanitized)) {
                        return $desc;
                    }
                }
            }
        }

        return $description;
    }

    public function getPrescriptionTypes($concept, $records)
    {
        $types = [];

        foreach ($records as $record) {
            if ($product_type = array_get($record, 'openfda.product_type.0')) {
                if ($product_type == 'HUMAN PRESCRIPTION DRUG') {
                    $types['1'] = true;
                } elseif ($product_type == 'HUMAN OTC DRUG') {
                    $types['2'] = true;
                }
            }
        }

        return array_keys($types);
    }

    public function getDrugLabels($concept)
    {
        $labels = [];

        $rxcui = $concept['rxcui'];
        $brand = $concept['sanitized_brand'];
        $generic = $concept['sanitized_generic'];

        $terms = [
            ['field' => 'openfda.brand_name', 'value' => $brand],
            ['field' => 'openfda.generic_name', 'value' => $generic],
            ['field' => 'openfda.rxcui', 'value' => $rxcui]
            ];

        if ($search = $this->getSearchQuery($terms)) {
            $results = $this->get("drug/label.json?search={$search}&limit=100");
            $results = array_get($results, 'results', []);

            foreach ($results as $res) {
                $res['rxcuis'] = array_get($res, 'openfda.rxcui', []);
                $res['sanitized_brand'] = $this->sanitizeName(array_get($res, 'openfda.brand_name.0', ''));
                $res['sanitized_generic'] = $this->sanitizeName(array_get($res, 'openfda.generic_name.0', ''));

                if (in_array($rxcui, $res['rxcuis']) ||
                    ($brand && $res['sanitized_brand'] && $brand == $res['sanitized_brand']) ||
                    ($generic && $res['sanitized_generic'] && $generic == $res['sanitized_generic'])) {
                    $labels[] = $res;
                }
            }
        }

#        echo $rxcui . ': SELECTED ' . count($labels) . ' labels out of ' . count($results) . " [" . $brand . "]\n";
        return $labels;
    }

    public function getRecalls($concept)
    {
        $recalls = [];

        $terms = [['field' => 'openfda.rxcui', 'value' => $concept['rxcui']]];

        if ($search = $this->getSearchQuery($terms)) {
            $results = $this->get("drug/enforcement.json?search={$search}+AND+status:Ongoing&limit=5");

            foreach (array_get($results, 'results', []) as $result) {
                $recalls[] = [
                    'number' => $result['recall_number'],
                    'date' => $result['recall_initiation_date'],
                    'recall' => $result['reason_for_recall'],
                    'lots' => $result['code_info']
                    ];
            }
        }

        return $recalls;
    }

    public function getIndications($concept)
    {
        $indications = [];

        $terms = [
            ['field' => 'patient.drug.openfda.rxcui', 'value' => $concept['rxcui']],
            ['field' => 'patient.drug.medicinalproduct', 'value' => $concept['sanitized_brand']],
            ['field' => 'patient.drug.medicinalproduct', 'value' => $concept['sanitized_generic']]
        ];

        if ($search = $this->getSearchQuery($terms)) {
            $results = $this->get("drug/event.json?search={$search}&count=patient.drug.drugindication.exact");

            foreach (array_get($results, 'results', []) as $result) {
                $indications[$result['term']] = $result['count'];
            }
        }

        return array_keys($indications);
    }

    public function getSideEffects($concept)
    {
        $side_effects = [];

        $terms = [
            ['field' => 'patient.drug.medicinalproduct', 'value' => $concept['sanitized_brand']],
            ['field' => 'patient.drug.medicinalproduct', 'value' => $concept['sanitized_generic']]
        ];

        if ($search = $this->getSearchQuery($terms)) {
            $results = $this->get("drug/event.json?search={$search}&count=patient.reaction.reactionmeddrapt.exact");

            foreach (array_get($results, 'results', []) as $result) {
                $side_effects[$result['term']] = $result['count'];
            }
        }

        return array_keys($side_effects);
    }

    private function getSearchQuery($terms = [])
    {
        $saerch = [];

        foreach ($terms as $term) {
            if (!empty($term['field']) && !empty($term['value'])) {
                $search[] = $term['field'] . ':"' . $term['value'] . '"';
            }
        }

        return join('+', $search);
    }
}
