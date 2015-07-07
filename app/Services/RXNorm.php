<?php

namespace App\Services;

class RXNorm extends RestAPI
{
    protected $api_base_uri = 'http://rxnav.nlm.nih.gov/REST/Prescribe/';
    protected $rate_limit = 20;
    

    private function getAllConcepts($query)
    {
        $results = $this->get("allconcepts.json?{$query}");

        return array_get($results, 'minConceptGroup.minConcept', []);
    }

    private function getProperties($rxcui)
    {
        $results = $this->get("rxcui/{$rxcui}/properties.json");

        return array_get($results, 'properties', []);
    }

    private function getAllProperties($rxcui, $query)
    {
        $results = $this->get("rxcui/{$rxcui}/allProperties.json?{$query}");

        return array_get($results, 'propConceptGroup.propConcept', []);
    }

    private function getRelated($rxcui, $query)
    {
        $results = $this->get("rxcui/{$rxcui}/related.json?{$query}");

        return array_get($results, 'relatedGroup.conceptGroup', []);
    }

    private function getAllRelated($rxcui)
    {
        $results = $this->get("rxcui/{$rxcui}/allrelated.json");

        return array_get($results, 'allRelatedGroup.conceptGroup', []);
    }

    public function getApproximateTerm($term)
    {
        $results = $this->get("approximateTerm.json?term=" . urlencode($term) . "&maxEntries=1");

        return array_get($results, 'approximateGroup.candidate.0.rxcui', false);
    }

    public function getAllBrands()
    {
        return $this->getAllConcepts('tty=BN+BPCK');
    }

    public function getConceptFromName($name)
    {
        $properties = [];

        if ($rxcui = $this->getApproximateTerm($name)) {
            $properties = $this->getConceptProperties($rxcui);
        }

        return $properties;
    }

    public function getConceptProperties($rxcui)
    {
        return $this->getProperties($rxcui);
    }

    public function getConceptRelations($rxcui)
    {
        $ttys = [];

        $related = $this->getAllRelated($rxcui);
        foreach ($related as $relations) {
            $tty = $relations['tty'];
            foreach (array_get($relations, 'conceptProperties', []) as $relation) {
                if ($relation['rxcui'] != $rxcui) {
                    $ttys[$tty]['ids'][] = $relation['rxcui'];
                    $ttys[$tty]['names'][] = $relation['name'];
                }
            }
        }

        return $ttys;
    }

    public function getRelatedConcepts($rxcui)
    {
        $concepts = [];

        $related = $this->getRelated($rxcui, 'tty=BN+BPCK+MIN');
        foreach ($related as $rel) {
            foreach (array_get($rel, 'conceptProperties', []) as $concept) {
                $concepts[] = $concept['rxcui'];
            }
        }

        return $concepts;
    }

    public function getConceptCodes($rxcui)
    {
        $codes = [];

        $properties = $this->getAllProperties($rxcui, 'prop=codes');
        foreach ($properties as $code) {
            $codes[$code['propValue']] = $code['propName'];
        }

        return $codes;
    }
}
