<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{

    public function index()
    {
        $faqs = Faq::orderBy('id', 'DESC')->paginate(10);
        return view('backend.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('backend.faq.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'string|required',
            'content' => 'string|required',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $status = Faq::create($data);

        if ($status) {
            request()->session()->flash('success', 'FAQ berhasil ditambahkan');
        } else {
            request()->session()->flash('error', 'Terjadi kesalahan saat menambah FAQ');
        }
        return redirect()->route('faq.index');
    }
    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('backend.faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);
        $this->validate($request, [
            'title' => 'string|required',
            'content' => 'string|required',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $status = $faq->fill($data)->save();

        if ($status) {
            request()->session()->flash('success', 'FAQ berhasil diupdate');
        } else {
            request()->session()->flash('error', 'Terjadi kesalahan saat update FAQ');
        }
        return redirect()->route('faq.index');
    }


    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $status = $faq->delete();

        if ($status) {
            request()->session()->flash('success', 'FAQ berhasil dihapus');
        } else {
            request()->session()->flash('error', 'Gagal menghapus FAQ');
        }
        return redirect()->route('faq.index');
    }
}