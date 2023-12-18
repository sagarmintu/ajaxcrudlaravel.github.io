<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::select('*');
        if($request->ajax())
        {
            return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                return '<a href="javascript:void(0)" class="btn btn-sm btn-success editBtn" data-id="'.$row->id.'">Edit</a> 
                <a href="javascript:void(0)" class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
        }
    }
    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {

        if ($request->category_id != null) 
        {
            $categories = Category::find($request->category_id);
            if(! $categories)
            {
                abort(404);
            }
            $categories->update([
                'name'=> $request->name,
                'type'=> $request->type
            ]);
            return response()->json([
                'success' => 'Category Updated Successfully'
            ], 201);
        } 
        else 
        {
            $request->validate([
                'name' => 'required|min:2|max:30',
                'type' => 'required'
            ]);
    
            Category::create([
                'name'=> $request->name,
                'type'=> $request->type
            ]);
    
            return response()->json([
                'success' => 'Category Added Successfully'
            ], 201);
        }
    }

    public function edit($id)
    {
       // return $id;
       $categories = Category::find($id);
       if(! $categories)
       {
         abort(404);
       }
       return $categories;
    }

    public function destroy($id)
    {
        //return $id;
        $categories = Category::find($id);
        if(! $categories)
        {
            abort(404);
        }
        $categories->delete();
        return response()->json([
            'success' => 'Category Deleted Successfully'
        ], 201);
    }
}