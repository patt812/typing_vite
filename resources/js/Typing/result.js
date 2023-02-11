
export default class Result {

  constructor() {
    this.ids = [];
    this.avarages = [];
    this.maxes = [];
    this.mins = [];
    this.encountereds = [];
    this.finisheds = [];
    this.accuracies = [];
    this.perfects = [];
    this.missStreaks = [];
    this.recent5WPMs = [];
    this.recent5ACCs = [];
    this.aveWpmMistakes = [];
    this.aveWpmPerfects = [];
    this.waiting = new Set();
  }

  popComma(base, concat) {
    base = concat + ',' + base;
    return base.slice(0, base.lastIndexOf(','));
  }

  calcAverage(val) {
    val = val.split(',');
    const divisor = val.length - val.filter((item) => item === '0').length;
    return val.reduce((sum, element) => {
      return sum + parseInt(element);
    }, 0) / divisor;
  }

  update(ID, missStreak, wpm, acc) {
    const i = this.ids.indexOf(ID);
    this.recent5ACCs[i] =
      this.popComma(this.recent5ACCs[i], Math.ceil(acc));
    this.recent5WPMs[i] =
      this.popComma(this.recent5WPMs[i], wpm);
    this.accuracies[i] = this.calcAverage(this.recent5ACCs[i]);
    this.avarages[i] = this.calcAverage(this.recent5WPMs[i]);
    this.finisheds[i]++;
    if (this.maxes[i] < wpm) {
      this.maxes[i] = wpm;
    }
    if (this.mins[i] === null || this.mins[i] > wpm) {
      this.mins[i] = wpm;
    }
     if (this.missStreaks[i] == null || this.missStreaks[i] < missStreak) {
      this.missStreaks[i] = missStreak;
    }
    if (Math.floor(acc) === 100) {
      this.aveWpmPerfects[i] =
        (this.aveWpmPerfects[i] + wpm) / ++this.perfects[i];
    } else {
      this.aveWpmMistakes[i] = (this.aveWpmMistakes[i] + wpm) /
        (this.finisheds[i] - this.perfects[i]);
    }
  }
}
