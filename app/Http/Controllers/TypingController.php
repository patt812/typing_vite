<?php

namespace App\Http\Controllers;

use App\Models\Sentence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class TypingController extends Controller
{
    public function show(Request $request)
    {
        $sentences = Auth::user()->sentences;

        return Inertia::render('Dashboard', [
            'sentences' => $sentences,
            'filled' => false,
        ]);
    }

    public function showSentence(Request $request)
    {
        $sentences = Auth::user()->sentences;
        $flush_message = $request->session()->get('flush_message');

        return Inertia::render('Sentence/Show', [
            'sentences' => $sentences,
            'status' => $flush_message
        ]);
    }

    public function getSentences(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            // 'sentences' =>  Auth::user()->randomSentences($request->num)
            'sentences' =>  fn () => Auth::user()->randomSentences($request->num),
            'filled' => fn () => true,
        ]);
    }

    public function storeSentence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sentence' => ['required', 'string', 'max:255'],
            'kana' => ['required', 'string', 'max:255', 'regex:/^[ぁ-ゞァ-ヾ！-／：-＠［-｀｛-～]+$/'],
        ]);
        $validator->validate();

        $sentence = new Sentence();
        $sentence->fill([
            'user_id' => Auth::id(),
            'sentence' => $request->sentence,
            'kana' => $request->kana,
        ])->save();

        session()->flash('flush_message', '登録しました。');
    }

    public function updateSentence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sentence' => ['required', 'string', 'max:255'],
            'kana' => ['required', 'string', 'max:255', 'regex:/^[ぁ-ゞァ-ヾ！-／：-＠［-｀｛-～]+$/'],
        ]);
        $validator->validate();

        $sentence = Sentence::find($request->id);
        $sentence->fill([
            'user_id' => Auth::id(),
            'sentence' => $request->sentence,
            'kana' => $request->kana,
        ])->update();

        session()->flash('flush_message', '更新しました。');
    }

    public function deleteSentence(Request $request)
    {
        Sentence::find($request->id)->delete();
        session()->flash('flush_message', '削除しました。');
    }

}
