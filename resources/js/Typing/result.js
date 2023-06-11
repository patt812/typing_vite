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

  static popComma(base, concat) {
    const concatBase = `${concat},${base}`;
    return concatBase.slice(0, concatBase.lastIndexOf(','));
  }

  static calcAverage(val) {
    const splitVal = val.split(',');
    const divisor = splitVal.length - splitVal.filter((item) => item === '0').length;
    return splitVal.reduce((sum, element) => sum + parseInt(element, 10), 0) / divisor;
  }

  update(i, missStreak, wpm, acc) {
    this.recent5ACCs[i] = Result.popComma(this.recent5ACCs[i], Math.ceil(acc));
    this.recent5WPMs[i] = Result.popComma(this.recent5WPMs[i], wpm);
    this.accuracies[i] = Result.calcAverage(this.recent5ACCs[i]);
    this.avarages[i] = Result.calcAverage(this.recent5WPMs[i]);
    this.finisheds[i] += 1;
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
      this.perfects[i] += 1;
      this.aveWpmPerfects[i] = (this.aveWpmPerfects[i] + wpm) / this.perfects[i];
    } else {
      this.aveWpmMistakes[i] = (this.aveWpmMistakes[i] + wpm)
        / (this.finisheds[i] - this.perfects[i]);
    }
  }
}
