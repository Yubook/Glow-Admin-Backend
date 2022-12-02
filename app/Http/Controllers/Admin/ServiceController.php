<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use DB;

use function App\Helpers\commonUploadImage;
use function App\Helpers\deleteOldImage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $services = Service::with('category', 'subcategory')->get();
            return view('admin.service.index', compact('services'));
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
            $categories = Category::where('is_active', 1)->get();
            return view('admin.service.create', compact('categories'));
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
    public function store(StoreServiceRequest $request)
    {
        DB::beginTransaction();
        try {
            $service = new Service();
            $service->name = $request->name;
            $service->category_id = $request->category_id;
            $service->subcategory_id = $request->subcategory_id;
            $service->time = $request->time;

            if ($request->file('image')) {
                $image = $request->file('image');
                $storage_path = "service";
                $imgpath = commonUploadImage($storage_path, $image);
                $service->image = $imgpath;
            }
            $service->save();

            DB::commit();
            return redirect()->route('services.index')->with('message', trans('message.service.create'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        try {
            $service = Service::with('category', 'subcategory')->where('id', $service->id)->first();
            return view('admin.service.show', compact('service'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        try {
            $service = Service::with('subcategory')->where('id', $service->id)->first();
            $categories = Category::where('is_active', 1)->get();
            return view('admin.service.edit', compact('service', 'categories'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        DB::beginTransaction();
        try {
            $service = Service::find($service->id);
            $service->name = $request->name;
            $service->category_id = $request->category_id;
            $service->subcategory_id = $request->subcategory_id;
            $service->time = $request->time;

            if ($request->file('image')) {
                deleteOldImage($service->image);
                $image = $request->file('image');
                $storage_path = "service";
                $imgpath = commonUploadImage($storage_path, $image);
                $service->image = $imgpath;
            }
            $service->save();

            DB::commit();
            return redirect()->route('services.index')->with('message', trans('message.service.update'));
        } catch (\Exception $e) {
            DB::rollback();
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $services = Service::find(request('ids'));
            $services->delete();
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
            $service = Service::find($request->ids);
            if (empty($service->is_active)) {
                $service->is_active = 1;
            } else {
                $service->is_active = 0;
            }
            $service->save();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }
}
