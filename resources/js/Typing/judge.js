export default class Judge {
  constructor() {
    this.isCorrect = false;
    this.allowExtra_N = false;
    this.goNext = false;
    this.patternIndex = 0;
    this.charIndex = 0;
    this.correctPatterns = [];
  }

  do(input, roma) {
    const inputRoma = roma;
    // 「N」を「NN」,「N'」と打てる場合は、正解とする
    if (this.allowExtra_N && (input === 'N' || input === '\'')) {
      this.allowExtra_N = false;
      this.patternIndex -= 1;
      inputRoma[this.patternIndex] = [`N${input}`];
      this.focusNextPattern(inputRoma);
      this.isCorrect = true;
      return;
    }

    // 入力中のかなの全パターンを参照し、現在位置のアルファベットと入力があっていれば正解
    for (const pattern of inputRoma[this.patternIndex]) {
      if (pattern[this.charIndex] === input) {
        this.correctPatterns.push(pattern);
      }
    }

    // 正解パターンに要素がない場合は不正解
    if (this.correctPatterns.length <= 0) {
      this.isCorrect = false;
      return;
    }
    this.isCorrect = true;
    this.allowExtra_N = false;

    // 入力位置が、正解パターンのうちの最小文字数であれば、次の文章へ移ってもよい
    if (Judge.minimum(this.correctPatterns) - 1 === this.charIndex) {
      // 文字列が「ん」の場合は、特別処理を行う
      if (this.correctPatterns.indexOf('N') >= 0) {
        // 文末でないなら、次をNで打ってもよい
        if (inputRoma.length !== this.patternIndex) {
          this.allowExtra_N = true;
        }
        // 「ん」を最短のnで入力した場合、必ず[N]が要素先頭に来るようにする
        if (this.correctPatterns[0] !== 'N') {
          this.correctPatterns.push(this.correctPatterns[0]);
          this.correctPatterns[0] = this.correctPatterns[this.correctPatterns.indexOf('N')];
          this.correctPatterns.splice(this.correctPatterns.lastIndexOf('N'), 1);
        }
      }
      // 入力結果を反映させる
      inputRoma[this.patternIndex] = this.correctPatterns;
      this.focusNextPattern(inputRoma);
      return;
    }
    // 正解であるが次の文字に移れない場合、次のパターンを参照するよう設定
    this.charIndex += 1;
    inputRoma[this.patternIndex] = this.correctPatterns;
    this.correctPatterns = [];
  }

  focusNextPattern(roma) {
    this.correctPatterns = [];
    this.charIndex = 0;
    this.patternIndex += 1;
    if (roma.length === this.patternIndex) {
      this.patternIndex = 0;
      this.goNext = true;
    }
  }

  static minimum(array) {
    let minimum = array[0].length;
    if (array.length === 1) return minimum;
    for (const str of array) {
      if (str.length < minimum) {
        minimum = str.length;
      }
    }
    return minimum;
  }
}
