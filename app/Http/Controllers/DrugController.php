<?php

namespace App\Http\Controllers;

use App\Facades\OpenFDA;

class DrugController extends Controller
{
    /**
     * Get drug detail data from ndc id.
     *
     * @param int $ndc
     */
    public function show($ndc)
    {
        $fda_info = json_decode(OpenFDA::getDrugInfo($ndc), true);

        //here munge our local data with response from open fda
        $response = [
            'fda_info' => $fda_info,
        ];

        return $response;
    }
}
