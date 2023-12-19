<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; //untuk memakai Facade File
use Illuminate\Support\Facades\Session; //untuk memakai Facade Session
use Illuminate\Validation\Rule; //untuk memakai fitur validasi Rule

use App\Models\Listing; //untuk memakai Model Listing

class ListingController extends Controller
{
    public function index(Request $request) {
        return view('listings.index', [
            'heading' => 'Latest Listings',
            'listings' => Listing::latest()->filter(request(['tag', 'search']))->simplePaginate(4)
        ]);
    }

    public function detail(Listing $listing) { //Dependency Injection mengambil data Model (Listing) dengan menggunakan id-nya (lalu didapatkan object $listing)
        return view('listings.detail', [
            'listing' => $listing
        ]); 
    }

    public function create() {
        return view('listings.create');
    }

    public function store(Request $request) {
        $formFields = $request->validate([
            'title' => 'required', //required artinya tidak boleh kosong
            'company' => ['required', Rule::unique('listings', 'company')], //pakai bantuan Rule:unique(), artinya data ga boleh sama, parameter-nya array ['table_name', 'field_name']
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'], //email artinya harus dalam bentuk email yg valid
            'tags' => 'required',
            'description' => 'required',
        ]);
        if($request->hasFile('logo')) { //cek apakah ada file yg di-upload?
            $formFields['logo'] = $request->file('logo')->store('logos', 'public'); //aksi simpan file, sekaligus me-return path-nya dimasukkan ke variable
        }
        $formFields['user_id'] = auth()->id(); 
        Listing::create($formFields); //INSERT to listings table
        return redirect('/')->with('message', 'Listing created successfully!'); //kalo udah selesai maka redirect ke path /home
    }

    public function edit(Listing $listing) {
        return view('listings.edit', [
            'listing' => $listing
        ]);
    }

    public function update(Request $request, Listing $listing) {
        if($listing->user_id != auth()->id()) { //pastikan listing tersebut di-update oleh user yg benar (yg sedang login sekarang)
            abort(403, 'Unauthorized Action!');
        } //ini fungsinya untuk mem-protect saja, bisa jadi ada yg iseng tiba - tiba bisa edit listing yg bukan punya user itu, jadi kita pertahankan lagi di logic ini
        $formFields = $request->validate([
            'title' => 'required', //required artinya tidak boleh kosong
            'company' => ['required'], //peraturan Rule::unique('listings', 'company') dihilangkan, karena bisa jadi orangnya tidak ingin ubah nama company-nya, karena kalo ada Rule:unique() maka ga bisa lanjut kalo value company-nya sama
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'], //email artinya harus dalam bentuk email yg valid
            'tags' => 'required',
            'description' => 'required',
        ]);
        if($request->hasFile('logo')) {
            if($listing->logo) {
                File::delete(storage_path('app/public/' . $listing->logo));
            }
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }
        Listing::where('id', $listing->id)->update($formFields); //ini cara memakai method pada Model-nya
        return redirect('/listings/' . $listing->id)->with('message', 'Listing updated successfully!');
    }

    public function delete(Listing $listing) {
        if($listing->user_id != auth()->id()) {
            abort(403, 'Unauthorized Action!');
        }
        $listing->delete();
        return redirect('/')->with('message', 'Listing deleted successfully!');
    }

    public function manage() {
        return view('listings.manage', ['listings' => auth()->user()->listings()->get()]);
    }
}
