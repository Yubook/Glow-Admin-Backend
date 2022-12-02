<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Subcategory;
use DB;
use PhpParser\Node\Stmt\Catch_;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $categories = Category::get();
            return view('admin.category.index', compact('categories'));
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
            return view('admin.category.create');
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
    public function store(StoreCategoryRequest $request)
    {
        try {

            $cat_name = strtolower($request->name);
            $check = Category::where('name', 'LIKE', '%' . $cat_name . '%')->first();
            if ($check) {
                return redirect()->back()->with('error', 'Category name already exits.')->withInput();
            }

            $category = new Category();
            $category->name = $cat_name;
            $category->save();

            return redirect()->route('categories.index')->with('message', trans('message.category.create'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        try {
            return view('admin.category.show', compact('category'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        try {
            return view('admin.category.edit', compact('category'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $cat_name = strtolower($request->name);
            $check = Category::where('name', 'LIKE', '%' . $cat_name . '%')->where('id', '!=', $category->id)->first();
            if ($check) {
                return redirect()->back()->with('error', 'Category name already exits.')->withInput();
            }
            $category = Category::find($category->id);
            $category->name = $cat_name;
            $category->save();

            return redirect()->route('categories.index')->with('message', trans('message.category.update'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        try {
            $category = Category::find(request('ids'));
            $category->delete();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function switchUpdate(Request $request)
    {
        try {
            $category = Category::find($request->ids);
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

    public function getSubcategory(Request $request)
    {
        try {
            $sub_category = Subcategory::where('category_id', $request->category_id)->where('is_active', 1)->get();
            $html = '';
            foreach ($sub_category as  $value) {
                $html .= "<option value=".$value->id.">$value->name</option>";
            }
            return response()->json(['success' => 1, 'html' => $html]);
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }
}
