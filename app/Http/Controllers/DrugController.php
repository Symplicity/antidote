<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function index(Request $request)
    {
        $limit = 15;

        if ($request['limit']) {
            $limit = $request['limit'];
        }

        if ($keywords = $request['keywords']) {
            $drugs = Drug::where('label', 'LIKE', '%' . $keywords . '%')->orWhere('description', 'LIKE', '%' . $keywords . '%')->orderBy('label', 'ASC')->paginate($limit);
        } else {
            $drugs = Drug::orderBy('label', 'ASC')->paginate($limit);
        }

        return $drugs;
    }

    /**
     * Get reviews for a drug by local (primary key) id.
     *
     * @param int $id
     */
    public function getReviews($id, Request $request)
    {
        $limit = 15;

        if ($request['limit']) {
            $limit = $request['limit'];
        }

        $reviews = Drug::find($id)->reviews()->with('user')->with('drug')->with('sideEffects')->orderBy('created_at', 'DESC')->paginate($limit);
        return $reviews;
    }

    /**
     * Add a review for a drug by id.
     *
     * @param int $id
     */
    public function addReview($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], 400);
        }

        try {
            $drug_review = new DrugReview();
            $drug_review->drug_id = $id;
            $drug_review->user_id = $request['user']['sub'];
            $drug_review->rating = $request->input('rating');
            $drug_review->comment = $request->input('comment');
            $drug_review->is_covered_by_insurance = $request->input('is_covered_by_insurance');

            if ($drug_review->save() && $side_effects = $request->input('side_effects')) {
                $drug_review->sideEffects()->sync($side_effects);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return $drug_review;
    }
}
