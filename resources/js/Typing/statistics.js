export default class Statistics {
  constructor() {
    this.accuracy = 0;
    this.currentWPM = 0;
    this.time = 0;
    this.previousTime = 0;
    this.thisStarttime = 0;
    this.gameTime;
    this.correct = 0;
    this.totalCorrect = 0;
    this.mistake = 0;
    this.totalMistake = 0;
    this.missStreak = 0;
    this.maxMissStreak = 0;
  }

  next() {
    this.missStreak = 0;
    this.PrevCorrect = this.correct;
    this.PrevMistake = this.mistake;
    this.mistake = 0;
    this.correct = 0;
    this.currentAccuracy = 0;
    this.accuracy = 0;
    this.currentWPM = 0;
  }

  startTimer() {
    this.previousTime = performance.now();
    this.gameTime = setInterval(() => {
      this.time = ((performance.now() - this.previousTime) / 1000).toFixed(3);
    }, 50);

  }

  splitTimer() {
    this.thisStarttime = this.time;
  }

  stopTimer() {
    clearInterval(this.gameTime);
  }

  getTotalWPM() {
    const result = this.totalCorrect / (this.time / 60);
    if (isNaN(result)) {
      console.error(`NaN: correct:${this.totalCorrect} time:${this.time - this.thisStarttime}`);
    }
    if (!isFinite(result)) return 6000;
    return Number(result.toFixed(2));
  }

  // WPM = 区間正答数 / (区間経過秒数 / 60)
  getcurrentWPM() {
    const result = this.correct / ((this.time - this.thisStarttime) / 60);
    if (isNaN(result)) {
      console.error(`NaN: correct:${this.correct} time:${this.time - this.thisStarttime}`);
    }
    if (!isFinite(result)) return 6000;
    return Number(result.toFixed(2));
  }

  calcAccuracy(correct, mistake) {
    if (correct === 0 || correct <= mistake) return 0;
    const accuracy = (((correct - mistake) * 100) / correct).toFixed(2);
    return Number(accuracy);
  }

  afterJudge(isCorrect) {
    if (isCorrect) {
      this.totalCorrect++;
      this.accuracy = this.calcAccuracy(++this.correct, this.mistake);
      this.currentWPM = this.getcurrentWPM(this.correct, this.mistake);
    } else {
      this.mistake++;
      this.totalMistake++;
      this.missStreak++;
      if (this.maxMissStreak < this.missStreak) {
        this.maxMissStreak = this.missStreak;
      }
    }
  }
}
