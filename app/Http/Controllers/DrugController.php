<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\Drug;
use App\Facades\User;
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
        return Drug::with('sideEffects')->with('indications')->with('prescriptionTypes')->find($id);
    }

    public function index(Request $request)
    {
        $limit = $this->getLimit($request);

        $drugs = Drug::with('sideEffects');

        if ($keywords = $request['keywords']) {
            $drugs = $drugs->where('label', 'LIKE', '%' . $keywords . '%')->orWhere('description', 'LIKE', '%' . $keywords . '%');
        }

        $drugs = $drugs->orderBy('label', 'ASC')->paginate($limit);

        return $drugs;
    }

    public function autocompleteSearch(Request $request)
    {
        $term = $request->input('term');
        $limit = $this->getLimit($request);

        //disable extra appends specified in the model
        \App\Drug::$without_appends = true;

        $drugs = Drug::select('id', 'label', 'generic')->where('label', 'LIKE', $term . '%');

        if (!empty($request->input('include_generics'))) {
            $drugs = $drugs->orWhere('generic', 'LIKE', $term . '%');
        }

        $drugs = $drugs->limit($limit)->orderBy('label', 'ASC')->get('label', 'id');
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

        $reviews = DrugReview::where('drug_id', $id)
            ->with('sideEffects')
            ->orderBy('upvotes_cache', 'DESC')
            ->orderBy('downvotes_cache', 'ASC')
            ->paginate($limit);

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

            $user = User::find($request['user']['sub']);

            $drug_review->age = $user->age;
            $drug_review->gender = $user->gender;//save here redundantly to avoid unnecessary join on read
            $drug_review->rating = $request->input('rating');
            $drug_review->comment = $request->input('comment');
            $drug_review->is_covered_by_insurance = $request->input('is_covered_by_insurance');

            if ($drug_review->save() && $side_effects = $request->input('side_effects')) {
                $drug_review->sideEffects()->sync($side_effects);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json($drug_review, 201);
    }

    /**
     * Get alternative drugs for a drug by local (primary key) id.
     *
     * @param int $id
     */
    public function getAlternatives($id, Request $request)
    {
        $limit = $this->getLimit($request);

        $reviews = Drug::find($id)->alternatives()->with('sideEffects')->orderBy('label', 'DESC')->paginate($limit);
        return $reviews;
    }
}
