<?php

namespace App\Services;

class RXNorm extends RestAPI
{
    protected $api_base_uri = 'http://rxnav.nlm.nih.gov/REST/Prescribe/';
    protected $rate_limit = 20;

    public function getConceptLabel($properties)
    {
        $label = array_get($properties, 'synonym', '');
        
        if (!$label) {
            $label = array_get($properties, 'name', '');
        }
        
        return $label;
    }
    
    public function getConceptProperties($rxcui)
    {
        return $this->getProperties($rxcui);
    }

    public function getAllBrands()
    {
        return $this->getAllConcepts('tty=BN+BPCK');
    }

    public function getRelatedBrands($rxcui)
    {
        $concepts = [];
        
        $related = $this->getRelated($rxcui, 'tty=BN+BPCK');
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
    
    public function getConceptRelations($properties)
    {
        $relations = [];
        
        $ttys = $this->getTTYs($properties);
        if (!empty($ttys)) {
            $relations['ingredients'] = array_get($ttys, 'IN', []);
            $relations['dose_forms'] = array_get($ttys, 'DF', []);
            $relations['drugs_components'] = array_get($ttys, 'SBD', []);
            $relations['related'] = array_merge(array_get($ttys, 'BN', []), array_get($ttys, 'BPCK', []));
            
            if (in_array($properties['tty'], ['BN', 'BPCK'])) {
                if (empty($ttys['PIN']) && !empty($ttys['IN'][0])) {
                    $relations['ingredient'] = $ttys['IN'][0];
                } elseif (count($ttys['PIN']) > 1 && !empty($ttys['MIN'][0])) {
                    $relations['ingredient'] = $ttys['MIN'][0];
                } else {
                    $relations['ingredient'] = $ttys['PIN'][0];
                }
                
                $relations['related'][] = $relations['ingredient'];
            }
            
            
            $relations['related'] = array_unique($relations['related']);
        }

        return $relations;
    }

    
    private function getTTYs($properties)
    {
        $ttys = [];
        
        $rxcui = $properties['rxcui'];
        
        $related = $this->getAllRelated($rxcui);
        foreach ($related as $relations) {
            if (!empty($relations['conceptProperties'])) {
                $tty = $relations['tty'];
                foreach ($relations['conceptProperties'] as $relation) {
                    if ($relation['rxcui'] != $rxcui) {
                        switch ($tty) {
                        case 'DF': {
                            $ttys[$tty][] = $relation['name'];
                            break;
                        }
                        case 'SBD': {
                            $ttys[$tty][] = [
                                'id' => $relation['rxcui'],
                                'label' => $this->getConceptLabel($relation)
                                ];
                            break;
                        }
                        default:
                            $ttys[$tty][] = $relation['rxcui'];
                        }
                    }
                }
            }
        }

        return $ttys;
    }

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
    
    private function getAllRelated($rxcui)
    {
        $results = $this->get("rxcui/{$rxcui}/allrelated.json");
        
        return array_get($results, 'allRelatedGroup.conceptGroup', []);
    }
    
    private function getRelated($rxcui, $query)
    {
        $results = $this->get("rxcui/{$rxcui}/related.json?{$query}");
        
        return array_get($results, 'relatedGroup.conceptGroup', []);
    }
    
    private function getAllProperties($rxcui, $query)
    {
        $results = $this->get("rxcui/{$rxcui}/allProperties.json?{$query}");
        
        return array_get($results, 'propConceptGroup.propConcept', []);
    }
}
