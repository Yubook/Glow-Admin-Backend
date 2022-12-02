<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Subcategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSubcategoryRequest;
use App\Http\Requests\UpdateSubcategoryRequest;
use DB;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $categories = Subcategory::with('category')->get();
            return view('admin.subcategory.index', compact('categories'));
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
            return view('admin.subcategory.create', compact('categories'));
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
    public function store(StoreSubcategoryRequest $request)
    {
        try {
            $cat_name = strtolower($request->name);
            $check = Subcategory::where('name', 'LIKE', '%' . $cat_name . '%')->first();
            if ($check) {
                return redirect()->back()->with('error', 'Sub Category name already exits.')->withInput();
            }
            $check_cat_name = Category::where('name', 'LIKE', '%' . $cat_name . '%')->first();
            if ($check_cat_name) {
                return redirect()->back()->with('error', 'Category name already exits.')->withInput();
            }

            $category = new Subcategory();
            $category->category_id = $request->category_id;
            $category->name = $cat_name;
            $category->save();

            return redirect()->route('subcategories.index')->with('message', trans('message.subcategory.create'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Subcategory  $subcategory
     * @return \Illuminate\Http\Response
     */
    public function show(Subcategory $subcategory)
    {
        try {
            return view('admin.subcategory.show', compact('subcategory'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subcategory  $subcategory
     * @return \Illuminate\Http\Response
     */
    public function edit(Subcategory $subcategory)
    {
        try {
            $categories = Category::where('is_active', 1)->get();
            return view('admin.subcategory.edit', compact('subcategory', 'categories'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subcategory  $subcategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory)
    {
        try {
            $cat_name = strtolower($request->name);
            $check = Subcategory::where('name', 'LIKE', '%' . $cat_name . '%')->where('id', '!=', $subcategory->id)->first();
            if ($check) {
                return redirect()->back()->with('error', 'Sub Category name already exits.')->withInput();
            }
            $check_cat_name = Category::where('name', 'LIKE', '%' . $cat_name . '%')->first();
            if ($check_cat_name) {
                return redirect()->back()->with('error', 'Category name already exits.')->withInput();
            }

            $category = Subcategory::find($subcategory->id);
            $category->category_id = $request->category_id;
            $category->name = $cat_name;
            $category->save();

            return redirect()->route('subcategories.index')->with('message', trans('message.subcategory.update'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subcategory  $subcategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subcategory $subcategory)
    {
        try {
            $category = Subcategory::find(request('ids'));
            $category->delete();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function switchUpdate(Request $request)
    {
        try {
            $category = Subcategory::find($request->ids);
            if (empty($category->is_active)) {
                $category->is_active = 1;
            } else {
                $category->is_active = 0;
            }
            $category->save();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }
}
