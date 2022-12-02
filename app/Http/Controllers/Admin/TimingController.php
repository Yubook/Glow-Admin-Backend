<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTimingRequest;
use App\Http\Requests\UpdateTimingRequest;
use App\Timing;
use DB;

use function App\Helpers\commonUploadImage;

class TimingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $timings = Timing::get();
            return view('admin.time.index', compact('timings'));
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
            return view('admin.time.create');
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
    public function store(StoreTimingRequest $request)
    {
        DB::beginTransaction();
        try {
            $timing = new Timing();
            $timing->time = $request->time;
            $timing->save();

            DB::commit();
            return redirect()->route('timings.index')->with('message', trans('message.timing.create'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function addExtraTime(Request $request)
    {
        DB::beginTransaction();
        try {
            $check = Timing::where('time', $request->extra_time)->first();
            if (!$check) {
                $timing = new Timing();
                $timing->time = $request->extra_time;
                $timing->type = 1;
                $timing->save();
            }

            DB::commit();
            return redirect()->back()->with('message', trans('message.timing.create'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Timing  $timing
     * @return \Illuminate\Http\Response
     */
    public function show(Timing $timing)
    {
        try {
            return view('admin.time.show', compact('timing'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Timing  $timing
     * @return \Illuminate\Http\Response
     */
    public function edit(Timing $timing)
    {
        try {
            return view('admin.time.edit', compact('timing'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Timing  $timing
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimingRequest $request, Timing $timing)
    {
        DB::beginTransaction();
        try {
            $timing = Timing::find($timing->id);
            $timing->time = $request->time;
            $timing->save();

            DB::commit();
            return redirect()->route('timings.index')->with('message', trans('message.timing.update'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Timing  $timing
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $timing = Timing::find(request('ids'));
            $timing->delete();
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
            $timing = Timing::find($request->ids);
            if (empty($timing->is_active)) {
                $timing->is_active = 1;
            } else {
                $timing->is_active = 0;
            }
            $timing->save();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }
}
