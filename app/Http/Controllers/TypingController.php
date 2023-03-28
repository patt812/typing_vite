<?php

namespace App\Http\Controllers;

use App\Models\Sentence;
use App\Models\SettingPlay;
use App\Models\SettingPreference;
use App\Models\Stats;
use App\Models\UserStat;
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
        $sentences = Sentence::with('stat')->where('user_id', Auth::id())->get();

        $flush_message = $request->session()->get('flush_message');

        return Inertia::render('Preference/Show', [
            'sentences' => $sentences,
        ]);
    }

    public function showStats()
    {
        $sentences = Sentence::with('stat')->where('user_id', Auth::id())->get();
        $user_stats = UserStat::where('user_id', Auth::id())->first();

        return Inertia::render('Stats/Show', [
            'sentences' => $sentences,
            'userStats' => $user_stats,
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
            $key = $sentences->search(function ($item) use ($result, $i) {
                return $item->id === $result['ids'][$i];
            });
            if ($key !== null && $sentences[$key]->stat) {
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

        $total_stats = $request->stats;
        DB::transaction(function () use ($inserts, $total_stats, $result) {
            $user_stats = UserStat::where('user_id', Auth::id())->first();
            if (!$user_stats->played) {
                $accuracy = $total_stats['accuracy'];
                $wpm = $total_stats['totalWPM'];
            } else {
                $accuracy = ($user_stats->accuracy + $total_stats['accuracy']) / 2;
                $wpm = ($user_stats->wpm + $total_stats['totalWPM']) / 2;
            }

            $user_stats->fill([
                'played' => $user_stats->played + 1,
                'typed' => $user_stats->typed + $total_stats['totalCorrect'] + $total_stats['totalMistake'],
                'accuracy' => $accuracy,
                'wpm' => $wpm,
                'max_wpm' => max(array_merge([$user_stats->max_wpm], $result['avarages'])),
                'played_seconds' => $user_stats->played_seconds + $total_stats['time']
            ])->save();

            Stats::insert($inserts);
        });
    }

    public function storeSentence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sentence' => ['required', 'string', 'max:255'],
            'kana' => ['required', 'string', 'max:255', 'regex:/^[ぁ-ゞァ-ヾ！-／：-＠［-｀｛\-～ー\d]+$/'],
        ]);
        $validator->validate();

        $sentence = new Sentence();
        $sentence->fill([
            'user_id' => Auth::id(),
            'sentence' => $request->sentence,
            'kana' => $request->kana,
        ])->save();

        session()->flash('message', '登録しました。');
    }

    public function storeSentences(Request $request)
    {
        $sentences = $request->sentences;

        $inserts = [];
        foreach ($sentences as $sentence) {
            $inserts[] = [
                'user_id' => Auth::id(),
                'sentence' => $sentence['sentence'],
                'kana' => $sentence['kana'],
            ];
        }

        DB::transaction(function () use ($inserts) {
            Sentence::insert($inserts);
        });

        session()->flash('message', count($sentences) . '件の文章を登録しました。');
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

        session()->flash('message', '更新しました。');
    }

    public function deleteSentence(Request $request)
    {
        Sentence::find($request->id)->delete();
        session()->flash('message', '削除しました。');
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

    public function resetAllStats()
    {
        DB::transaction(function () {
            $user = Auth::user();

            $sentences = Sentence::where('user_id', $user->id)->pluck('id')->toArray();
            Stats::whereIn('sentence_id', $sentences)->delete();

            $user->total_stats()->update([
                'played' => 0,
                'typed' => 0,
                'accuracy' => 0,
                'wpm' => 0,
                'max_wpm' => 0,
                'played_seconds' => 0,
            ]);
        });

        session()->flash('message', '削除しました。');
    }


    public function storePreference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sentences' => ['required', 'numeric'],
            'prior_no_stats' => ['required'],
            'is_random' => ['required', 'boolean',],
            'min_accuracy' => ['nullable', 'numeric'],
            'max_accuracy' => ['nullable', 'numeric'],
            'min_wpm' => ['nullable', 'numeric'],
            'max_wpm' => ['nullable', 'numeric'],
        ]);
        $validator->validate();

        $update = [
            'is_random' => $request->is_random,
            'sentences' => $request->sentences,
            'limit_wpm' => $request->limit_wpm,
            'limit_accuracy' => $request->limit_accuracy,
            'prior_no_stats' => $request->prior_no_stats,
        ];

        if ($request->limit_wpm) {
            $update['min_wpm'] = $request->min_wpm;
            $update['max_wpm'] = $request->max_wpm;
        }
        if ($request->limit_accuracy) {
            $update['min_accuracy'] = $request->min_accuracy;
            $update['max_accuracy'] = $request->max_accuracy;
        }

        DB::transaction(function () use ($update) {
            SettingPreference::updateOrInsert(
                ['setting_id' => Auth::user()->settings->id],
                $update
            );
        });

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

    public function setSound(Request $request, SettingPlay $setting_play)
    {
        DB::transaction(function () use ($request, $setting_play) {
            $setting_play->fill($request->all())->save();
        });
        session()->flash('message', 'サウンドを設定しました。');
    }
}
