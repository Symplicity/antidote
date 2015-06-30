<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;

class RXNorm
{
    private $client;
    private $api_base_uri = 'http://rxnav.nlm.nih.gov/REST/Prescribe';
    private $rate_limit = 8;

    private static $requests = 0;

    public function __construct(Client $client = null)
    {
        $this->client = $client;
        $this->rate_limit = env('RXNORM_RATE_LIMIT', $this->rate_limit);
    }

    public function getLabel($prop)
    {
        return empty($prop['synonym']) ? $prop['name'] : $prop['synonym'];
    }

    private function limitRate()
    {
        $this->requests++;

        if ($this->rate_limit && $this->requests % $this->rate_limit === 0) {
            sleep(1);
        }
    }

    private function fetch($query, $index = '')
    {
        $data = [];

        $this->limitRate();

        try {
            $json = $this->client->get($this->base_uri . $query);
            $res = json_decode($json, true);

            foreach (explode('.', $index) as $i) {
                if (empty($res[$i])) {
                    break;
                } else {
                    $res = $res[$i];
                }
            }

            if (!empty($res)) {
                $data = $res;
            }
        } catch (Exception $e) {
            // skip errors
        }

        return $data;
    }

    public function getAllConcepts($query)
    {
        $query = "/allconcepts.json?{$query}";

        $brans = $this->fetch($query, 'minConceptGroup.minConcept');
    }

    public function getProperties($rxcui)
    {
        $query = "/rxcui/{$rxcui}/properties.json";

        return $this->fetch($query, 'properties');
    }

    public function getAllRelated($rxcui)
    {
        $query = "/rxcui/{$rxcui}/allrelated.json";

        return $this->fetch($query, 'allRelatedGoup.conceptGroup');
    }

    public function getRelated($rxcui, $query)
    {
        $query = "/rxcui/{$rxcui}/related.json?{$query}";

        return $this->fetch($query, 'relatedGroup.conceptGroup');
    }

    public function getAllProperties($rxcui, $query)
    {
        $query= "/rxcui/{$rxcui}/allProperties.json?{$query}";

        return $this->fetch($query, 'propConceptGroup.propConcept');
    }

    public function getBrands()
    {
        return $this->getAllConcepts('tty=BN+BPCK');
    }

    public function getCodes($rxcui)
    {
        $codes = [];

        $code_data = $this->getAllProperties($rxcui, 'prop=codes');
        foreach ($code_data as $code) {
            $codes[$code['propValue']] = $code['propName'];
        }

        return $codes;
    }

    public function getRelations($properties)
    {
        $relations = ['related' => []];

        $rxcui = $properties['rxcui'];
        $ttys = $this->getTTYs($rxcui);

        if (!empty($ttys['IN'])) {
            $relations['ingredients'] = $ttys['IN'];
        }

        if (!empty($ttys['DF'])) {
            sort($ttys['DF']);
            $relations['dose_forms'] = $ttys['DF'];
        }

        if (!empty($ttys['SBD'])) {
            $relations['drugs'] = $ttys['SBD'];
        }

        if (!empty($ttys['BN'])) {
            $relations['related'] = array_merge($relations['related'], $ttys['BN']);
        }

        if (!empty($ttys['BPCK'])) {
            $relations['related'] = array_merge($relations['related'], $ttys['BPCK']);
        }

        if (in_array($properties['tty'], ['BN', 'BPCK'])) {
            if (empty($ttys['PIN'])) {
                $relations['ingredient'] = $ttys['IN'][0];
            } elseif (count($ttys['PIN']) > 1) {
                $relations['ingredient'] = $ttys['MIN'][0];
            } else {
                $relations['ingredient'] = $ttys['PIN'][0];
            }

            $relations['related'][] = $relations['ingredient'];
        }

        $relations['related'] = array_unique($relations['related']);

        return $relations;
    }

    private function getTTYs($rxcui)
    {
        $ttys = [];

        $relations = $this->getAllRelated($rxcui);
        foreach ($relations as $relation) {
            if (!empty($rel['conceptProperties'])) {
                foreach ($relation['conceptProperties'] as $rel) {
                    if ($r['rxcui'] != $rxcui) {
                        switch ($tty) {
                        case 'DF': {
                            $ttys[$tty][] = $r['name'];
                            break;
                        }
                        case 'SBD': {
                            $ttys[$tty][] = ['id' => $r['rxcui'], 'label' => getLabel($r)];
                            break;
                        }
                        default:
                            $ttys[$tty][] = $r['rxcui'];
                        }
                    }
                }
            }
        }

        return $ttys;
    }
}
