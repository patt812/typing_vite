import Statistics from './statistics.js';
import Judge from './judge.js';
import Sentence from './sentence.js';
import Result from './result.js';

export default class Game {

  constructor(countdown = 0) {
    this.DEFAULT_GAME_DIALOG = "スペースキーを押してスタート";
    this.isPlayable = false;
    this.isStarted = false;
    this.isFinished = false;
    this.played = 0;
    this.countdownSec = countdown;
    this.dialog = "";
    this.sentencesDone = 1;
    this.judge = new Judge();
    this.sentence = new Sentence();
    this.statistics = new Statistics();
    this.result = new Result();
  }

  async prepare(data) {
    while (data.length) {
      const datum = data.shift();
      this.sentence.kanasDisplay.push(datum.kana);
      this.sentence.sentences.push(datum.sentence);
      this.sentence.ids.push(datum.id);
    }

    await this.sentence.setRomas();
    this.isPlayable = true;
  }

  performJudge(input) {
    this.judge.do(input, this.sentence.romas[0]);
    this.statistics.afterJudge(this.judge.isCorrect);
  }

  async start(event) {
    if (event.code === "Space" && !this.typing.isStarted && this.typing.isPlayable) {
      if (this.typing.countdownSec) {
        await this.typing.countdown(this.typing.countdownSec);
      }
      this.typing.isStarted = true;
      if (!this.typing.played) {
        document.addEventListener('keydown', {
          handleEvent: this.typing.observe,
          typing: this.typing
        });
      }
      this.typing.played++;
      this.typing.statistics.startTimer();
    }
  }

  observe(event) {
    if (!this.typing.isStarted) return;
    if (event.key === "Escape") {
      this.typing.reset();
      return;
    }

    this.typing.performJudge(event.key);
    if (!this.typing.judge.isCorrect) {
      return;
    }

    if (this.typing.sentence.sentences.length == this.typing.sentence.current + 1
      && this.typing.judge.goNext) {

      this.typing.statistics.stopTimer();
      this.typing.isStarted = false;
      this.typing.isPlayable = false;
      this.typing.isFinished = true;
      return;
    } else if (this.typing.judge.goNext) {
      this.typing.next();
      this.typing.statistics.splitTimer();
      return;
    }

    this.typing.sentence.displayRoma = this.typing.sentence.joinRoma();
  }

  quit() {
    this.updateResult(true);
  }

  reset() {
    this.dialog = "";
    this.isStarted = false;
    this.isPlayable = false;
    this.judge = new Judge();
    this.sentence = new Sentence();
    this.statistics = new Statistics();
    this.result = new Result();
  }

  next() {
    this.sentence.romas.shift();
    this.judge.goNext = false;
    this.sentencesDone++;
    this.sentence.current++;
    this.statistics.next();
    this.sentence.displayRoma = this.sentence.joinRoma();
  }

  updateResult(opt_escaped = false) {
    const currentID = this.sentence.ids[this.sentence.current];
    if (!opt_escaped) {
      this.result.update(
        currentID,
        this.statistics.missStreak,
        this.statistics.currentWPM,
        this.statistics.accuracy,
      );
    }
    this.result.waiting.add(currentID);
  }

  async countdown(sec) {
    for (let i = sec; i > 0; i--) {
      this.dialog = i;
      await new Promise((resolve) => setTimeout(resolve, 1000));
    }
  };

  isStartKeyInput(input) {
    const totalInputs = this.statistics.totalCorrect +
      this.statistics.totalMistake;
    return totalInputs == 0 && input == 'Space';
  }
}
