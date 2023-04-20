import { shallowMount } from '@vue/test-utils';
import Stats from '@/Pages/Stats/Show.vue';

let wrapper;

jest.mock('axios', () => ({
    delete: jest.fn(() => Promise.resolve()),
}));

beforeEach(() => {
  wrapper = shallowMount(Stats, {
    props: {
      sentences: [],
      userStats: {
        wpm: 0,
        max_wpm: 0,
        accuracy: 0,
        typed: 0,
        played: 0,
        played_seconds: 0,
      },
    },
  });
});

describe('Stats.vue', () => {
  test('userStats object is properly set', () => {
    expect(wrapper.vm.userStats.wpm).toBe(0);
    expect(wrapper.vm.userStats.max_wpm).toBe(0);
    expect(wrapper.vm.userStats.accuracy).toBe(0);
    expect(wrapper.vm.userStats.typed).toBe(0);
    expect(wrapper.vm.userStats.played).toBe(0);
    expect(wrapper.vm.userStats.played_seconds).toBe(0);
  });

  test('formatSeconds function returns formatted time', () => {
    expect(wrapper.vm.formatSeconds(3665)).toBe('1:01:05');
  });

  test('statsCount computed property returns the correct count', async () => {
    await wrapper.setProps({
      sentences: [
        { stat: { id: 1 } },
        { stat: { id: 2 } },
        { stat: null },
      ],
    });
    expect(wrapper.vm.statsCount).toBe(2);
  });

  test('renders the correct stats when a sentence is selected', async () => {
    wrapper.vm.selected = {
      stat: {
        played: 5,
        perfect: 2,
        min_wpm: 30,
        max_wpm: 60,
        ave_wpm: 45,
        min_accuracy: 70,
        max_accuracy: 95,
        ave_accuracy: 80,
        max_miss_streak: 3,
      },
      sentence: 'テスト文章',
    };

    expect(wrapper.vm.selected.stat.played).toBe(5);
    expect(wrapper.vm.selected.stat.perfect).toBe(2);
    expect(wrapper.vm.selected.stat.min_wpm).toBe(30);
    expect(wrapper.vm.selected.stat.max_wpm).toBe(60);
    expect(wrapper.vm.selected.stat.ave_wpm).toBe(45);
    expect(wrapper.vm.selected.stat.min_accuracy).toBe(70);
    expect(wrapper.vm.selected.stat.max_accuracy).toBe(95);
    expect(wrapper.vm.selected.stat.ave_accuracy).toBe(80);
    expect(wrapper.vm.selected.stat.max_miss_streak).toBe(3);
  });
});
