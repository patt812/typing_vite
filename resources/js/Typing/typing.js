import Statistics from './statistics.js';
import Judge from './judge.js';
import Sentence from './sentence.js';
import Result from './result.js';

export default class Game {

  constructor(settings = {}) {
    this.countdownSec = settings.countdown ? settings.countdown : 0;
    console.log(settings);
    this.volume = settings.volume ? settings.volume : 0.5;
    this.use_type_sound = settings.use_type_sound ? settings.use_type_sound : false;
    this.use_beep_sound = settings.use_beep_sound ? settings.use_beep_sound : false;
    if (this.use_type_sound) {
        this.typeSound = new Audio(import.meta.env.VITE_TYPING_SOUND_TYPE);
        this.typeSound.volume = this.volume;
    }
    if (this.use_beep_sound) {
        this.beepSound = new Audio(import.meta.env.VITE_TYPING_SOUND_BEEP);
        this.beepSound.volume = this.volume;
    }

    this.DEFAULT_GAME_DIALOG = "スペースキーを押してスタート";
    this.isPlayable = false;
    this.isStarted = false;
    this.isFinished = false;
    this.played = 0;
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
      this.result.ids.push(datum.id);
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
    if (!this.typing.isStarted || event.metaKey || event.ctrlKey || event.key == "F5") {
        return;
    }
    if (event.key === "Escape") {
      this.typing.reset();
      return;
    }

    this.typing.performJudge(event.key.toLowerCase());
    if (!this.typing.judge.isCorrect) {
        if (this.typing.use_beep_sound) {
            this.typing.beepSound.load();
            this.typing.beepSound.play();
        }
      return;
    }
    if (this.typing.use_type_sound) {
        this.typing.typeSound.load();
        this.typing.typeSound.play();
    }

    if (this.typing.sentence.sentences.length == this.typing.sentence.current + 1
      && this.typing.judge.goNext) {

      this.typing.statistics.stopTimer();
      this.typing.sentence.current++;
      this.typing.updateResult();
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
    this.updateResult();
    this.statistics.next();
    this.sentence.displayRoma = this.sentence.joinRoma();
  }


  updateResult(opt_escaped = false) {
    const currentID = this.sentence.ids[this.sentence.current - 1];
    if (!opt_escaped) {
      this.result.update(
        this.sentence.current - 1,
        this.statistics.missStreak,
        this.statistics.currentWPM,
        this.statistics.accuracy,
      );
    }
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

  changeVolume(volume) {
    this.volume = volume;
    this.typeSound.volume = this.volume;
    this.beepSound.volume = this.volume;
  }
}
