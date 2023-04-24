<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = Category::get();
        if ($request->keyword) {
            $products = Product::search($request->keyword)->paginate(10);
        } else {
            $products = Product::with('category')->paginate(10);
        }

        return view('product.index', compact('products', 'category'))->with('no', 1);
    }

    public function create()
    {
        $categorys = Category::select('id', 'nama_kategori')->get();
        return view('product.create', compact('categorys'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_item' => 'required',
            'id_kategori' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'size' => 'nullable',
            'size_stone' => 'nullable',
            'qty_stone' => 'nullable',
        ]);

        $input = $request->all();
        if ($image = $request->file('image')) {
            $destinationPath = 'image/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['image'] = "$profileImage";
        }

        Product::create($input);

        return redirect()->route('products.index')->with('success', 'Success adding product 🥳');
    }

    public function show($id)
    {
        return view('product.slug', [
            'product' => Product::findOrFail($id),
        ]);
    }

    public function edit(string $id)
    {
        $product = Product::findOrfail($id);
        $categorys = Category::get();

        return view('product.edit', compact('product', 'categorys'));
    }

    public function update(Request $request, $id)
    {
        if ($files = $request->file('image')) {
            $destinationPath = 'image/';
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $update['image'] = "$profileImage";
        }
        Product::where('id',$id)->update($update);

        return redirect()->route('products.index')->with('success', 'Success update product 😾👍');
    }

    public function destroy($id)
    {
        $product = Product::findOrfail($id);
        $product->delete();

        return redirect()->route('products.index')->with('Success', 'Success delete product 😾👍');
    }

    public function export()
    {
        return new ProductsExport;
    }
}
