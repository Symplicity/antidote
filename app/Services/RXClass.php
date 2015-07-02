<?php

namespace App\Services;

class RXClass extends RestAPI
{
    protected $api_base_uri = 'http://rxnav.nlm.nih.gov/REST/rxclass/';
    protected $rate_limit = 20;

    public function getConceptIndications($rxcui)
    {
        $indications = [];

        $diseases = $this->getByRxcui($rxcui, 'relas=may_treat+may_prevent');
        foreach ($diseases as $disease) {
            $indication = $disease['rxclassMinConceptItem']['className'];
        }

        return $indications;
    }

    public function getRelatedConcepts($rxcui)
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

    private function getByRxcui($rxcui, $query)
    {
        $results = $this->get("class/byRxcui.json?rxcui={$rxcui}&{$query}");

        return array_get($results, 'rxclassDrugInfoList.rxclassDrugInfo', []);
    }

    private function getClassMembers($classId, $query)
    {
        $results = $this->get("classMembers.json?classId={$classId}&{$query}");

        return array_get($results, 'drugMemberGroup.drugMember', []);
    }
}
