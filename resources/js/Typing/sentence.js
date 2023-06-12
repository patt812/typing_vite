import Patterns from './patterns';

export default class Sentence {
  constructor() {
    this.ids = [];
    this.sentences = [];
    this.kanas = [];
    this.kanasDisplay = [];
    this.romas = [];
    this.displayRoma = '';
    this.current = 0;
  }

  async setRomas() {
    await Patterns.initialize();
    for (const kana of this.kanasDisplay) {
      this.getPatterns(kana);
    }
    this.displayRoma = this.joinRoma();
  }

  getPatterns(inputChunk) {
    let chunk = inputChunk;
    const roma = [];
    const kana = [];
    while (chunk.length > 0) {
      let cutLength = 1;
      if (chunk.length >= 3 && Patterns.containsKey(chunk.substring(0, 3))) {
        cutLength = 3;
      } else if (chunk.length >= 2 && Patterns.containsKey(chunk.substring(0, 2))) {
        cutLength = 2;
      }
      kana.push(chunk.substring(0, cutLength));
      roma.push(Patterns.patternDictionary[chunk.substring(0, cutLength)].concat());
      chunk = chunk.slice(cutLength);
    }
    this.kanas.push(kana);
    this.romas.push(Sentence.checkN(kana, roma));
  }

  static checkN(kana, roma) {
    if (kana.indexOf('ん') === -1) {
      return roma;
    }
    // 末尾の「ん」は省略できないのでlength-1
    for (let i = 0; i < kana.length - 1; i += 1) {
      // 文字列が「ん」かつ 次の要素1文字目があ行、な行、や行または「ん」ではない
      if (kana[i] === 'ん' && kana[i + 1][0].match(/[^あ-おな-のや-よん]/)) {
        roma[i].push('N');
      }
    }
    return roma;
  }

  joinRoma() {
    const ret = [];
    const roma = this.romas[0];
    for (let i = 0; i < roma.length; i += 1) {
      ret.push(roma[i][0]);
    }
    return ret.join('');
  }
}
