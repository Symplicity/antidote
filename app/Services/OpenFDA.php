<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;

class OpenFDA
{
    private $client = null;
    private $api_base_uri = '';
    private $rate_limit = 4;

    private static $requests = 0;

    public function __construct(Client $client = null, $config)
    {
        // Create a client with a base URI
        $this->client = $client;
        $this->api_base_uri = $config['api_base_uri'];
        $this->api_key = $config['api_key'];

        if (!empty($config['rate_limit'])) {
            $this->rate_limit = $config['rate_limit'];
        } elseif (!empty($this->api_key)) {
            $this->rate_limit = 2; //40 requests per minute per ip
        }
    }

    public function sanitizeName($name)
    {
        if ($name && ($sanitized = preg_replace('/\+{2,}/', '+', preg_replace('/\W/', '+', $name)))) {
            return strtolower($sanitized);
        }
    }

    private function getBaseURI($type = 'drug.label')
    {
        $base_uri = '/' . str_replace('.', '/', $type) . '.json';

        return $this->api_base_uri . $base_uri;
    }

    private function getOptions()
    {
        $options = [];

        if ($this->api_key) {
            $options['api_key'] = $this->api_key;
        }

        return $options;
    }

    private function limitRate()
    {
        $this->requests++;

        if ($this->rate_limit && $this->requests % $this->rate_limit === 0) {
            sleep(1);
        }
    }

    private function fetch($query, $type = 'drug.label')
    {
        $data = [];

        $this->limitRate();

        try {
            $uri = $this->getBaseURI($type) . $query;
            $options = $this->getOptions();

            $json = $this->client->get($uri, $options);
            $res = json_decode($json, true);

            if (!empty($res['results'])) {
                $data = $res['results'];
            }
        } catch (Exception $e) {
            //skip errors
        }

        return $data;
    }

    private function getSearchQuery($terms = [])
    {
        $saerch = [];

        foreach ($terms as $term) {
            if (!empty($term['value']) && !empty($term['field'])) {
                $search[] = $term['field'] . ':"' . $term['value'] . '"';
            }
        }

        return join('+', $search);
    }

    public function getIndications($brand, $generic)
    {
        $indications = [];

        $terms = [
            ['field' => 'patient.drug.medicinalproduct', 'value' => $brand],
            ['field' => 'patient.drug.medicinalproduct', 'value' => $generic]
        ];

        if ($search = $this->getSearchQuery($terms)) {
            $query = "search={$search}&count=patient.drug.drugindication.exact";

            $results = $this->fetch($query, 'drug.event');
            foreach ($results as $result) {
                $indications[$result] = true;
            }
        }

        return array_keys($indications);
    }

    public function getSideEffects($brand, $generic)
    {
        $side_effects = [];

        $terms = [
            ['field' => 'patient.drug.medicinalproduct', 'value' => $brand],
            ['field' => 'patient.drug.medicinalproduct', 'value' => $generic]
        ];

        if ($search = $this->getSearchQuery($terms)) {
            $query = "search={$search}&count=patient.reaction.reactionmeddrapt.exact";

            $results = $this->fetch($query, 'drug.event');
            foreach ($results as $result) {
                $side_effects[$result] = true;
            }
        }

        return array_keys($side_effects);
    }

    public function getRecalls($rxcui)
    {
        $recalls = [];

        $terms = [
            ['field' => 'openfda.rxcui', 'value' => $rxcui]
        ];


        if ($search = $this->getSearchQuery($terms)) {
            $query = "search=openfda.rxcui={$rxcui}+AND+status:Ongoing";

            $results = $this->fetch($query, 'drug.enforcement');
            foreach ($results as $result) {
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

    public function getDrugLabels($brand, $generic)
    {
        $labels = [];

        $terms = [
            ['field' => 'openfda.brand_name', 'value' => $brand],
            ['field' => 'openfda.generic_name', 'value' => $generic]
        ];

        if ($search = $this->getSearchQuery($terms)) {
            $query = "search={$search}";

            $results = $this->fetch($query, 'drug.label');
            foreach ($results as $result) {
                $result_brand = $result['openfda']['brand_name'][0];
                $result_generic = $result['openfda']['generic_name'][0];

                $result['sanitized_brand'] = $this->sanitizeName($result_brand);
                $result['sanitized_generic'] = $this->sanitizeName($result_generic);

                if (($brand && $result['sanitized_brand'] && $brand == $result['sanitized_brand']) ||
                    ($generic && $result['sanitized_generic'] && $generic == $result['sanitized_generic'])) {
                    $labels[] = $result;
                }
            }
        }

        return $labels;
    }

    public function getPrescriptionTypes($records)
    {
        $types = [];

        foreach ($records as $record) {
            if (!empty($record['openfda']['product_type'])) {
                $product_type = $record['openfda']['product_type'][0];

                if ($product_type == 'HUMAN PRESCRIPTION DRUG') {
                    $type['1'] = true;
                } elseif ($product_type == 'HUMAN OTC DRUG') {
                    $type['2'] = true;
                }
            }
        }

        return array_keys($types);
    }

    public function getDescription($type, $name, $records)
    {
        $description = '';

        foreach ($records as $record) {
            $desc = $record['description'][0];
            $purpose = $record['purpose'][0];

            if ($name == $record['sanitized_' . $type]) {
                $description = empty($desc) ? $purpose : $desc;
            }

            if ($description) {
                break;
            }
        }

        return $description;
    }
}
