<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\Drug;
use App\Facades\User;
use App\Facades\DrugReview;
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
        $term = $request->input('term');
        $limit = $this->getLimit($request);

        //disable extra appends specified in the model
        \App\Drug::$without_appends = true;

        $drugs = Drug::select('id', 'label');

        if (!empty($term)) {
            if ($term === '#') {
                $drugs = $drugs->whereRaw(
                    "upper(left(label, 1)) not between 'A' and 'Z'"
                );
            } else {
                $drugs = $drugs->where('label', 'LIKE', $term . '%');
            }
        }

        $drugs = $drugs->orderBy('label', 'ASC')->simplePaginate($limit);

        return $drugs;
    }

    public function autocompleteSearch(Request $request)
    {
        $term = $request->input('term');
        $limit = $this->getLimit($request);

        //disable extra appends specified in the model
        \App\Drug::$without_appends = true;

        return Drug::select('id', 'label', 'generic')
            ->where('label', 'LIKE', $term . '%')
            ->orWhere('generic', 'LIKE', $term . '%')
            ->limit($limit)
            ->orderBy('label', 'ASC')
            ->get();
    }

    /**
     * Get reviews for a drug by local (primary key) id.
     *
     * @param int $id
     */
    public function getReviews($id, Request $request)
    {
        $limit = $this->getLimit($request);
        $min_age = $request->input('min_age');
        $max_age = $request->input('max_age');
        $gender = $request->input('gender');
        $user = $request->input('user');

        $reviews = DrugReview::where('drug_id', $id)
            ->with('sideEffects');

        if (!empty($min_age)) {
            $reviews = $reviews->where('age', '>=', $min_age);
        }

        if (!empty($max_age)) {
            $reviews = $reviews->where('age', '<=', $max_age);
        }

        if (!empty($gender)) {
            $reviews = $reviews->where('gender', $gender);
        }

        $fields = ['drug_reviews.*'];
        if ($user) {
            $reviews = $reviews->leftJoin('drug_review_votes', function ($join) use ($user) {
                $join->on('drug_reviews.id', '=', 'drug_review_votes.drug_review_id');
                $join->on('drug_review_votes.user_id', '=', \DB::raw($user));
            });

            $fields[] = 'drug_review_votes.vote';
        }

        $reviews = $reviews->orderBy('upvotes_cache', 'DESC')
            ->orderBy('downvotes_cache', 'ASC')
            ->simplePaginate($limit, $fields);

        return $reviews;
    }

    /**
     * Add a review for a drug by id.
     *
     * @param int $id
     */
    public function addReview($id, Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'rating' => 'required',
                'is_covered_by_insurance' => 'required'
            ],
            [
                'rating.required' => 'Effectiveness is required',
                'is_covered_by_insurance.required' => 'Insurance coverage is required',
            ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], 422);
        }

        try {
            $drug_review = DrugReview::getModel();

            $drug_review->drug_id = $id;
            $drug_review->user_id = $request['user']['sub'];

            $user = User::find($request['user']['sub']);

            $drug_review->age = $user->age;
            $drug_review->gender = $user->gender;//save here redundantly to avoid unnecessary join on read
            $drug_review->rating = $request->input('rating');
            $drug_review->comment = $request->input('comment');
            $drug_review->is_covered_by_insurance = $request->input('is_covered_by_insurance');

            if ($drug_review->save() && ($side_effects = $request->input('side_effects'))) {
                $drug_review->sideEffects()->sync($side_effects);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unexpected Error'], 400);
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

        $reviews = Drug::find($id)->alternatives()->with('sideEffects')->orderBy('label', 'ASC')->simplePaginate($limit);
        return $reviews;
    }
}
