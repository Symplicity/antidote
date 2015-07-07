<?php

namespace App\Services;

class MedicalTranslator
{
    private $skip = [
        'accidental drug intake by child' => true,
        'adverse event' => true,
        'condition aggravated' => true,
        'death' => true,
        'drug administration error' => true,
        'drug dose omission' => true,
        'drug exposure during pregnancy' => true,
        'drug ineffective' => true,
        'drug interaction' => true,
        'drug use for unknown indication' => true,
        'expired drug administered' => true,
        'fall' => true,
        'general physical health deterioration' => true,
        'ill-defined disorder' => true,
        'incorrect dose administered' => true,
        'incorrect drug administration duration' => true,
        'incorrect route of drug administration' => true,
        'increased international normalised ratio' => true,
        'infusion related reaction' => true,
        'intentional drug misuse' => true,
        'intentional misuse' => true,
        'intentional overdose' => true,
        'maternal exposure during pregnancy' => true,
        'medication error' => true,
        'miosis' => true,
        'no adverse event' => true,
        'off label use' => true,
        'pharmaceutical product complaint' => true,
        'post procedural complication' => true,
        'premedication' => true,
        'product quality issue' => true,
        'product substitution issue' => true,
        'product used for unknown indication' => true,
        'therapeutic response unexpected' => true,
        'unevaluable event' => true,
        'unresponsive to stimuli' => true,
        'wrong drug administered' => true,
        'wrong technique in drug usage process' => true,
        ];

    private $replace = [
        'anaemia' => 'anemia',
        'diarrhoea' => 'diarrhea',
        'discolouration' => 'discoloration',
        'haemorhage' => 'hemorhage',
        'increased blood' => 'increased',
        'leukaemia' => 'leukemia',
        'odour' => 'odor',
        'oedema' => 'edema',
        'oesophageal' => 'esophageal',
        ];

    private $order = [
        'abnormal',
        'acute',
        'blurred',
        'chronic',
        'congestive',
        'decreased' ,
        'identified',
        'impaired',
        'increased',
        'induced',
        'malignant',
        'metastatic',
        'peripheral',
        'poor',
        'reduced',
        'spontaneous',
        ];

    private $translations = [
        'abdominal pain lower' => 'abdominal pain',
        'abdominal pain upper' => 'abdominal pain',
        'acquired immunodeficiency syndrome' => 'aids',
        'ageusia' => 'loss of taste',
        'amyotrophic lateral sclerosis' => 'als',
        'ankylosing spondylitis' => 'inflammatory arthritis',
        'arthalgia' => 'joint pain',
        'ascites' => 'abdominal swelling',
        'asthenia' => 'weakness',
        'attention deficit/hyperactivity disorder' => 'adhd',
        'bacterial gram-negative' => 'bacterial infection',
        'breast cancer female' => 'breast cancer',
        'confusional state' => 'confusion',
        'colitis ulcerative' => 'inflammatory bowel disease',
        'completed suicide' => 'suicidal ideation',
        'diabetes mellitus non-insuling-dependent' => 'non insulin dependent diabetes',
        'diplaopia' => 'double vision',
        'drug abuser' => 'drug abuse',
        'dysgeusia' => 'loss of taste',
        'dyspepsia' => 'indigestion',
        'dyspnoea' => 'labored breathing',
        'endophthalmitis' => 'eye inflammation',
        'epistaxis' => 'nose bleed',
        'erythema' => 'skin redness',
        'faeces discoloured' => 'discolored feces',
        'fissure in ano' => 'anal fissure',
        'foetal exposure during pregnancy' => 'fetal exposure',
        'gastrooesophageal reflux disease' => 'acid reflux disease',
        'gram-positive bacterial infections' => 'bacterial infection',
        'hiv infection' => 'aids',
        'hypercholasterolaemia' => 'increased cholesterol',
        'hyperglycaemia' => 'hyperglycemia',
        'hyperhidrosis' => 'excessive sweating',
        'hyperlipidaemia' => 'elevated lipids',
        'hypoaesthesia' => 'numbness',
        'low density lipoprotein increased' => 'increased ldl',
        'multiple drug overdose' => 'overdose',
        'myalgia' => 'muscle pain',
        'nasopharyngitis' => 'nose and throat infection',
        'neuropathy peripheral' => 'peripheral neuropathy',
        'neutropenia' => 'neutrophil deficiency',
        'oedema peripheral' => 'peripheral edema',
        'osteoporosis postmenopausal' => 'postmenupausal osteoporosis',
        'paraesthesia' => 'numbness',
        'plearal effusion' => 'chest fluid buildup',
        'pruritis' => 'itching skin',
        'psychotic disorder' => 'psychosis',
        'psoriatic arthropathy' => 'psoriatic arthritis',
        'pyrexia' => 'fever',
        'rash maculo-papular' => 'rash',
        'rhabdomyolysis' => 'muscle tissue breakdown',
        'seborrheic dermatitis' => 'scaly skin patches',
        'sleep initiation and maintenance disorders' => 'sleep disorder',
        'somnolence' => 'drowsiness',
        'status asthmaticus' => 'asthma',
        'suicide attempt' => 'suicidal ideation',
        'thrombocytopenia' => 'low platelet count',
        'tobacco user' => 'tobacco abuse',
        'toxicity to various agents' => 'toxicity',
        'transient ischaemic attack' => 'stroke',
        'urticaria' => 'skin rash',
        'urinary tract infection' => 'uti',
        'wound infection staphylococcal' => 'wound infection',
        'xanthopsia' => 'vision deficiency',
        'xerosis' => 'dry skin',
        ];

    private function fixCommaOrder($term)
    {
        $translation = $term;

        $pos = strpos($translation, ', ');
        if ($pos !== false) {
            $translation = substr($translation, $pos + 2) . ' ' . substr($translation, 0, $pos);
        }

        return $translation;
    }

    private function fixWordOrder($term)
    {
        $translation = $term;

        foreach ($this->order as $word) {
            $pos = strpos($translation, ' ' . $word);
            if ($pos !== false) {
                $translation = $word . ' ' . substr($translation, 0, $pos);
                break;
            }
        }
        
        return $translation;
    }
    
    private function replaceWords($term)
    {
        $translation = $term;

        foreach ($this->replace as $from => $to) {
            $translation = str_replace($from, $to, $translation);
        }

        return $translation;
    }
    
    public function translate($term)
    {
        $translation = '';

        if ($term) {
            $term = strtolower($term);

            if (!isset($this->skip[$term])) {
                if (empty($this->translations[$term])) {
                    $translation = $term;
                    $translation = $this->fixCommaOrder($translation);
                    $translation = $this->fixWordOrder($translation);
                    $translation = $this->replaceWords($translation);
                } else {
                    $translation = $this->translations[$term];
                }
            }
        }

        return $translation;
    }
}
