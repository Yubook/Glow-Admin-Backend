<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTermsPolicyRequest;
use App\Http\Requests\UpdateTermsPolicyRequest;
use App\TermsPolicy;
use DB;

class TermsPolicyController extends Controller
{

    public function index()
    {
        try {
            $terms = TermsPolicy::get();

            return view('admin.term_policy.index', compact('terms'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('admin.term_policy.create');
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function store(StoreTermsPolicyRequest $request)
    {
        DB::beginTransaction();
        try {
            $terms = new TermsPolicy();
            $terms->selection = $request->selection;
            $terms->description = $request->description;
            $terms->for = $request->for;
            $terms->save();

            DB::commit();
            return redirect()->route('terms.index')->with('message', trans('message.terms.create'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $termspolicy = TermsPolicy::find($id);
            return view('admin.term_policy.show', compact('termspolicy'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $termspolicy = TermsPolicy::find($id);
            return view('admin.term_policy.edit', compact('termspolicy'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function update(UpdateTermsPolicyRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $terms = TermsPolicy::find($id);
            $terms->selection = $request->selection;
            $terms->description = $request->description;
            $terms->for = $request->for;
            $terms->save();

            DB::commit();
            return redirect()->route('terms.index')->with('message', trans('message.terms.update'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
    }

    public function switchUpdate(Request $request)
    {
        try {
            $terms = TermsPolicy::find($request->ids);
            if (empty($terms->is_active)) {
                $terms->is_active = 1;
            } else {
                $terms->is_active = 0;
            }
            $terms->save();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }
}
