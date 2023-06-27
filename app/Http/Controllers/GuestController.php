<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Sentence;
use App\Models\Stats;
use App\Models\UserStat;
use App\Rules\NoDuplicateKana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class GuestController extends Controller
{
    private const GUEST_STATS_TEMPLATE = [
        "played" => 0,
        "min_wpm" => null,
        "max_wpm" => null,
        "ave_wpm" => null,
        "min_accuracy" => null,
        "max_accuracy" => null,
        "ave_accuracy" => null,
        "perfect" => 0,
        "max_miss_streak" => 0,
    ];

    public function login(Request $request)
    {
        if ($request->session()->has('guest_data')) {
            $request->session()->forget('guest_data');
        }

        $sentence_path = resource_path('sentence/guest-template.json');
        $sentences = json_decode(File::get($sentence_path), true);

        foreach ($sentences as $index => &$sentence) {
            $sentence['id'] = $index;
            $sentence['stat'] = self::GUEST_STATS_TEMPLATE;
        }

        $guest_data = ['sentence' => $sentences];
        $guest_data['total_stats'] = [
            'played' => 0,
            'typed' => 0,
            'accuracy' => 0,
            'wpm' => 0,
            'max_wpm' => 0,
            'played_seconds' => 0,
        ];

        $request->session()->put('guest_data', $guest_data);

        return redirect(route('guest.dashboard'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget('guest_data');

        return redirect(route('login'));
    }

    public function register(Request $request)
    {
        $creator = new CreateNewUser();
        if ($request->take_over) {
            $request->session()->put('take_over', true);
        }
        $user = $creator->create($request->all());

        if ($request->take_over) {
            DB::transaction(function () use ($request, $user) {
                foreach ($request->session()->get('guest_data')['sentence'] as $sentence) {
                    $newSentence = new Sentence();
                    $newSentence->user_id = $user->id;
                    $newSentence->sentence = $sentence['sentence'];
                    $newSentence->kana = $sentence['kana'];
                    $newSentence->save();

                    if ($sentence['stat']['played']) {
                        $newStat = new Stats();
                        $newStat->sentence_id = $newSentence->id;
                        $newStat->played = $sentence['stat']['played'];
                        $newStat->min_wpm = $sentence['stat']['min_wpm'];
                        $newStat->max_wpm = $sentence['stat']['max_wpm'];
                        $newStat->ave_wpm = $sentence['stat']['ave_wpm'];
                        $newStat->min_accuracy = $sentence['stat']['min_accuracy'];
                        $newStat->max_accuracy = $sentence['stat']['max_accuracy'];
                        $newStat->ave_accuracy = $sentence['stat']['ave_accuracy'];
                        $newStat->perfect = $sentence['stat']['perfect'];
                        $newStat->max_miss_streak = $sentence['stat']['max_miss_streak'];
                        $newStat->save();
                    }
                }

                $totalStats = $request->session()->get('guest_data')['total_stats'];
                $user_stats = UserStat::where('user_id', $user->id)->first();
                $user_stats->fill($totalStats)->save();
            });
        }

        $request->session()->forget('take_over');
        $request->session()->forget('guest_data');

        Auth::login($user);
        return redirect(route('dashboard'));
    }

    public function show(Request $request)
    {
        $sentences = $request->session()->get('guest_data')['sentence'];

        foreach ($sentences as $index => &$sentence) {
            $sentence['id'] = $index;
        }
        shuffle($sentences);

        return Inertia::render('Dashboard', [
            'sentences' => array_slice($sentences, 0, 5),
        ]);
    }

    public function showSentence(Request $request)
    {
        $sentences = $request->session()->get('guest_data')['sentence'];

        return Inertia::render('Sentence/ShowSentence', [
            'sentences' => $sentences,
        ]);
    }

    public function showPreference(Request $request)
    {
        $sentences = $request->session()->get('guest_data')['sentence'];

        return Inertia::render('Preference/ShowPreference', [
            'sentences' => $sentences,
        ]);
    }

    public function showStats(Request $request)
    {
        $sentences = $request->session()->get('guest_data')['sentence'];

        return Inertia::render('Stats/ShowStats', [
            'sentences' => $sentences,
        ]);
    }

    public function storeResult(Request $request)
    {
        $result = $request->result;
        $sentences = $request->session()->get('guest_data')['sentence'];
        $total_stats = $request->session()->get('guest_data')['total_stats'];

        for ($i = 0; $i < count($result['ids']); $i++) {
            $sentence = $sentences[$result['ids'][$i]];
            $stats = $sentence['stat'];

            $stats['played'] += 1;
            $stats['min_wpm'] = $stats['min_wpm'] === null
                ? $result['avarages'][$i] : min($stats['min_wpm'], $result['avarages'][$i]);
            $stats['max_wpm'] = $stats['max_wpm'] === null
                ? $result['avarages'][$i] : max($stats['max_wpm'], $result['avarages'][$i]);
            $stats['ave_wpm'] = ($stats['ave_wpm'] * ($stats['played'] - 1)
                + $result['avarages'][$i]) / $stats['played'];
            $stats['min_accuracy'] = $stats['min_accuracy'] === null
                ? $result['accuracies'][$i] : min($stats['min_accuracy'], $result['accuracies'][$i]);
            $stats['max_accuracy'] = $stats['max_accuracy'] === null
                ? $result['accuracies'][$i] : max($stats['max_accuracy'], $result['accuracies'][$i]);
            $stats['ave_accuracy'] = ($stats['ave_accuracy'] * ($stats['played'] - 1)
                + $result['accuracies'][$i]) / $stats['played'];
            if ($result['accuracies'][$i] === 100) {
                $stats['perfect']++;
            }
            $stats['max_miss_streak'] = max($stats['max_miss_streak'], $result['missStreaks'][$i]);

            $sentences[$result['ids'][$i]]['stat'] = $stats;
        }

        $totalStats = $request->stats;
        $current_accuracy = ($totalStats['totalCorrect'] * 100)
            / ($totalStats['totalCorrect'] + $totalStats['totalMistake']);
        if ($total_stats['played']) {
            $accuracy = ($total_stats['accuracy'] + $current_accuracy) / 2;
            $wpm = ($total_stats['wpm'] + $totalStats['totalWPM']) / 2;
        } else {
            $accuracy = $current_accuracy;
            $wpm = $totalStats['totalWPM'];
        }

        $total_stats['played'] += 1;
        $total_stats['typed'] += ($totalStats['totalCorrect'] + $totalStats['totalMistake']);
        $total_stats['accuracy'] = $accuracy;
        $total_stats['wpm'] = $wpm;
        $total_stats['max_wpm'] = max(array_merge([$total_stats['max_wpm']], $result['avarages']));
        $total_stats['played_seconds'] += $totalStats['time'];

        $request->session()->put('guest_data.sentence', $sentences);
        $request->session()->put('guest_data.total_stats', $total_stats);
    }

    public function storeSentence(Request $request)
    {
        $guest_data = $request->session()->get('guest_data');
        $sentences = $guest_data['sentence'];

        if (count($sentences) >= 10) {
            session()->flash('message', '10件以上登録できません。');
            return;
        }

        $validator = Validator::make($request->all(), [
            'sentence' => ['required', 'string', 'max:255'],
            'kana' => [
                'required',
                'string',
                'max:255',
                'regex:/^[ぁ-ゞァ-ヾ！-／：-＠［-｀｛\-～ー\d]+$/',
                new NoDuplicateKana($sentences),
            ],
        ]);
        $validator->validate();

        $new_sentence = [
            'id' => count($sentences),
            'sentence' => $request->sentence,
            'kana' => $request->kana,
            'stat' => self::GUEST_STATS_TEMPLATE,
        ];

        $guest_data['sentence'][] = $new_sentence;
        $request->session()->put('guest_data', $guest_data);

        session()->flash('message', '登録しました。');
    }

    public function updateSentence(Request $request)
    {
        $guest_data = $request->session()->get('guest_data');
        $index = $request->index;
        $sentence = $guest_data['sentence'][$index];

        $validator = Validator::make($request->all(), [
            'sentence' => ['required', 'string', 'max:255'],
            'kana' => [
                'required',
                'string',
                'max:255',
                'regex:/^[ぁ-ゞァ-ヾ！-／：-＠［-｀｛-～]+$/',
                new NoDuplicateKana($guest_data['sentence']),
            ],
        ]);
        $validator->validate();

        $sentence['sentence'] = $request->sentence;
        $sentence['kana'] = $request->kana;

        $guest_data['sentence'][$index] = $sentence;

        $request->session()->put('guest_data', $guest_data);

        session()->flash('message', '更新しました。');
    }

    public function deleteSentence(Request $request)
    {
        $guest_data = $request->session()->get('guest_data');
        $index = $request->index;

        unset($guest_data['sentence'][$index]);

        $guest_data['sentence'] = array_values($guest_data['sentence']);

        $request->session()->put('guest_data', $guest_data);

        session()->flash('message', '削除しました。');
    }

    public function resetStat(Request $request)
    {
        $index = (int) $request->route('sentence_id');
        $guest_data = $request->session()->get('guest_data');

        if ($request->delete_sentence) {
            unset($guest_data['sentence'][$index]);
        } else {
            $guest_data['sentence'][$index]['stat'] = self::GUEST_STATS_TEMPLATE;
        }

        $request->session()->put('guest_data', $guest_data);
        session()->flash('message', '削除しました。');
    }
}
