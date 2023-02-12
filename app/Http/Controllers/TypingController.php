<?php

namespace App\Http\Controllers;

use App\Models\Sentence;
use App\Models\SettingPreference;
use App\Models\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class TypingController extends Controller
{
    public function show(Request $request)
    {
        $sentences = Auth::user()->prepareSentences();

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

    public function showPreference(Request $request)
    {
        $sentences = Auth::user()->sentences;
        $flush_message = $request->session()->get('flush_message');

        return Inertia::render('Preference/Show', [
            'sentences' => $sentences,
        ]);
    }

    public function showStats()
    {
        $sentences = Sentence::with('stat')->where('user_id', Auth::id())->get();

        return Inertia::render('Stats/Show', [
            'sentences' => $sentences,
        ]);
    }

    public function getSentences(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'sentences' =>  fn () => Auth::user()->prepareSentences(),
            'filled' => fn () => true,
        ]);
    }

    public function storeResult(Request $request)
    {
        $result = $request->result;
        $sentences = Sentence::whereIn('id', $result['ids'])->get();
        $inserts = [];

        for ($i=0; $i < count($result['ids']); $i++) {
            $stat = [];
            if ($sentences[$i]->stat) {
                $stat = Stats::where('sentence_id', $result['ids'][$i])->first();
                $stat->sentence_id = $result['ids'][$i];
                $stat->played += 1;
                $stat->min_wpm = min($result['avarages'][$i], $stat['min_wpm']);
                $stat->max_wpm = max($result['avarages'][$i], $stat['max_wpm']);
                $stat->ave_wpm = ($stat->ave_wpm + $result['avarages'][$i]) / 2;
                $stat->min_accuracy = min($stat->min_accuracy, $result['accuracies'][$i]);
                $stat->max_accuracy = max($stat->max_accuracy, $result['accuracies'][$i]);
                $stat->ave_accuracy = ($stat->ave_accuracy + $result['accuracies'][$i]) / 2;
                $stat->max_miss_streak = max($stat->max_miss_streak, $result['missStreaks'][$i]);
                if ($result['accuracies'][$i] == 100) {
                    $stat->perfect += 1;
                }
                $stat->update();
            } else {
                $stat['sentence_id'] = $result['ids'][$i];
                $stat['played'] = 1;
                $stat['min_wpm'] = $result['avarages'][$i];
                $stat['max_wpm'] = $result['avarages'][$i];
                $stat['ave_wpm'] = $result['avarages'][$i];
                $stat['min_accuracy'] = $result['accuracies'][$i];
                $stat['max_accuracy'] = $result['accuracies'][$i];
                $stat['ave_accuracy'] = $result['accuracies'][$i];
                $stat['max_miss_streak'] = $result['missStreaks'][$i];
                $stat['created_at'] = now();
                $stat['updated_at'] = now();
                if ($result['accuracies'][$i] == 100) {
                    $stat['perfect'] = 1;
                } else {
                    $stat['perfect'] = 0;
                }
                $inserts[] = $stat;
            }
        }

        if (count($inserts)) {
            DB::transaction(function () use ($inserts) {
                Stats::insert($inserts);
            });
        }
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

    public function resetStat(Request $request, Sentence $sentence)
    {
        $deleted = DB::transaction(function () use ($request, $sentence) {
            if ($request->delete_sentence) {
                $deleted = $sentence->delete();
            } else {
                $deleted = $sentence->stat->delete();
            }
            return $deleted;
        });

        if ($deleted) {
            session()->flash('message', '削除しました。');
        }
    }


    public function storePreference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sentences' => ['required', 'numeric'],
            'is_random' => ['required'],
        ]);
        $validator->validate();

        SettingPreference::updateOrInsert(
            ['setting_id' => Auth::user()->settings->id],
            [
                'is_random' => $request->is_random,
                'sentences' => $request->sentences
            ]
        );

        session()->flash('message', '更新しました。');
    }

    public function storeSentencePreference(Request $request)
    {
        $selected = [];
        $unselected = [];

        foreach ($request->sentence as $sentence) {
            if ($sentence['is_selected']) {
                $selected[] = $sentence['id'];
            } else {
                $unselected[] = $sentence['id'];
            }
        }

        Sentence::whereIn('id', $selected)->where('user_id', Auth::id())
            ->update([
                'is_selected' => 1,
                'updated_at' => now()
            ]);
        Sentence::whereIn('id', $unselected)->where('user_id', Auth::id())
            ->update([
                'is_selected' => 0,
                'updated_at' => now()
            ]);

        session()->flash('message', '更新しました。');
    }
}
