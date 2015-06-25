<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\Drug;
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
        return Drug::with('sideEffects')->find($id);
    }

    public function index(Request $request)
    {
        $limit = $this->getLimit($request);

        $drugs = Drug::with('sideEffects');

        if ($keywords = $request['keywords']) {
            $drugs = $drugs->where('label', 'LIKE', '%' . $keywords . '%')->orWhere('description', 'LIKE', '%' . $keywords . '%');
        }

        if ($alpha = $request['alpha']) {
            $drugs = $drugs->where('label', 'LIKE', $alpha . '%');
        }

        $drugs = $drugs->orderBy('label', 'ASC')->paginate($limit);

        return $drugs;
    }

    /**
     * Get reviews for a drug by local (primary key) id.
     *
     * @param int $id
     */
    public function getReviews($id, Request $request)
    {
        $limit = $this->getLimit($request);

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

    /**
     * Get alternative drugs for a drug by local (primary key) id.
     *
     * @param int $id
     */
    public function getAlternatives($id, Request $request)
    {
        $limit = $this->getLimit($request);

        $reviews = Drug::find($id)->alternatives()->orderBy('label', 'DESC')->paginate($limit);
        return $reviews;
    }
}
