<?php

namespace app\Http\Controllers;

use App\Services\OpenFDA;

class DrugController extends Controller
{
    /**
     * Get drug detail data from ndc id.
     *
     * @param int $ndc
     */
    public function show($ndc)
    {
        $client = new OpenFDA();
        $fda_info = json_decode($client->getDrugInfo($ndc), true);

        //here munge our local data with response from open fda
        $response = [
            'fda_info' => $fda_info,
        ];

        return $response;
    }
}
