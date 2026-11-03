<?php

namespace App\Http\Controllers;

use App\Models\ChatTemplate;
use Illuminate\Http\Request;

class ChatTemplateController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->query('jenis', 'both'); // user | kurir | both
        $query = ChatTemplate::query()->urutan();

        if ($jenis !== 'both') {
            $query->jenis($jenis);
        }

        $templates = $query->get();

        return response()->json([
            'ok' => true,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'              => 'required|string|max:30|unique:chat_templates,kode',
            'nama'              => 'required|string|max:100',
            'isi'               => 'required|string|max:1000',
            'jenis'             => 'required|in:user,kurir,both',
            'otomatis'          => 'boolean',
            'trigger_kata_kunci'=> 'nullable|string|max:100',
            'urutan'            => 'nullable|integer|min:0',
        ]);

        $template = ChatTemplate::create($request->all());

        return response()->json(['ok' => true, 'template' => $template], 201);
    }

    public function update(Request $request, ChatTemplate $template)
    {
        $request->validate([
            'kode'              => 'sometimes|string|max:30|unique:chat_templates,kode,' . $template->id,
            'nama'              => 'sometimes|string|max:100',
            'isi'               => 'sometimes|string|max:1000',
            'jenis'             => 'sometimes|in:user,kurir,both',
            'otomatis'          => 'sometimes|boolean',
            'trigger_kata_kunci'=> 'sometimes|nullable|string|max:100',
            'urutan'            => 'sometimes|integer|min:0',
        ]);

        $template->update($request->all());

        return response()->json(['ok' => true, 'template' => $template]);
    }

    public function destroy(ChatTemplate $template)
    {
        $template->delete();
        return response()->json(['ok' => true]);
    }

    /**
     * Cari template otomatis yang cocok dengan kata kunci pesan masuk.
     * Digunakan untuk fitur auto-balas kurir.
     */
    public function cariOtomatis(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string|max:500',
            'jenis' => 'required|in:user,kurir',
        ]);

        $pesan = mb_strtolower($request->pesan);
        $jenis = $request->jenis;

        $template = ChatTemplate::query()
            ->jenis($jenis)
            ->otomatis()
            ->whereNotNull('trigger_kata_kunci')
            ->urutan()
            ->get()
            ->first(function ($t) use ($pesan) {
                $kunci = mb_strtolower($t->trigger_kata_kunci);
                return str_contains($pesan, $kunci);
            });

        if (!$template) {
            return response()->json(['ok' => false, 'message' => 'Template otomatis tidak ditemukan.']);
        }

        return response()->json(['ok' => true, 'template' => $template]);
    }
}