<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReasonRequest;
use App\Http\Requests\UpdateReasonRequest;
use App\Reason;
use DB;

use function App\Helpers\commonUploadImage;

class ReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $reasons = Reason::get();
            return view('admin.reason.index', compact('reasons'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            return view('admin.reason.create');
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReasonRequest $request)
    {
        DB::beginTransaction();
        try {
            $reason = new Reason();
            $reason->reason = $request->reason;
            $reason->save();

            DB::commit();
            return redirect()->route('reasons.index')->with('message', trans('message.reason.create'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reason  $reason
     * @return \Illuminate\Http\Response
     */
    public function show(Reason $reason)
    {
        try {
            return view('admin.reason.show', compact('reason'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reason  $reason
     * @return \Illuminate\Http\Response
     */
    public function edit(Reason $reason)
    {
        try {
            return view('admin.reason.edit', compact('reason'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reason  $reason
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReasonRequest $request, Reason $reason)
    {
        DB::beginTransaction();
        try {
            $reason = Reason::find($reason->id);
            $reason->reason = $request->reason;
            $reason->save();

            DB::commit();
            return redirect()->route('reasons.index')->with('message', trans('message.reason.update'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reason  $reason
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $reason = Reason::find(request('ids'));
            $reason->delete();
            DB::commit();
            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function switchUpdate(Request $request)
    {
        try {
            $reason = Reason::find($request->ids);
            if (empty($reason->is_active)) {
                $reason->is_active = 1;
            } else {
                $reason->is_active = 0;
            }
            $reason->save();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }
}
