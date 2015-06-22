<?php

namespace App\Http\Controllers;

use App\Facades\OpenFDA;
use App\Drug;
use App\DrugReview;
use Validator;

class DrugController extends Controller
{
    /**
     * Get drug detail data from id.
     *
     * @param int $id
     */
    public function show($id)
    {
        return Drug::find($id);
    }

    public function index()
    {
        $drugs = Drug::all();
        return ['data' => $drugs];
    }

    /**
     * Get reviews for a drug by local (primary key) id.
     *
     * @param int $id
     */
    public function getReviews($id)
    {
        $reviews = Drug::find($id)->reviews()->with('user')->with('drug')->get();
        return ['data' => $reviews];
    }
}
