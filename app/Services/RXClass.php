<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;

class RXClass
{
    private $client;
    private $api_base_uri = 'http://rxnav.nlm.nih.gov/REST/rxclass';
    private $rate_limit = 8;

    public function __construct(Client $client = null)
    {
        $this->client = $client;
        $this->rate_limig = env('RXCLASS_RATE_LIMIT', $this->rate_limit);
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

    public function getByRxcui($rxcui, $query)
    {
        $query = "/class/byRxcui.json?rxcui={$rxcui}&{$query}";

        return $this->fetch($query, 'rxclassDrugInfoList.rxclassDrugInfo');
    }

    public function getClassMembers($classId, $query)
    {
        $query = "/classMembers.json?classId={$classId}&{$query}";

        return $this->fetch($query, 'drugMemberGroup.drugMember');
    }

    public function getIndications($rxcui)
    {
        $indications = [];

        $diseases = $this->getByRxcui($rxcui, 'relas=may_treat+may_prevent');
        foreach ($diseases as $disease) {
            $indication = $disease['rxclassMinConceptItem']['className'];
        }

        return $indications;
    }

    public function getRelated($rxcui)
    {
        $concepts = [];

        $classes = $this->getByRxcui($rxcui, 'relaSource=ATC');
        foreach ($classes as $class) {
            $class_id = $class['rxclassMinConceptItem']['classId'];

            $members = $this->getClassMembers($class_id, 'relaSource=ATC');
            foreach ($members as $member) {
                $concepts[] = $member['minConcept']['rxcui'];
            }
        }

        return $concepts;
    }
}
