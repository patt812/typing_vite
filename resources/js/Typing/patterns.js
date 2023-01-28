import romaPatterns from './romaPatterns';

export default class Patterns {

  static patternDictionary = romaPatterns.patternDictionary;

  static defaultPattens = romaPatterns.defaultPattens;

  static isInitialized = false;

  static async initialize() {
    if (this.isInitialized) return;
    this.addLittle_tsu(this.patternDictionary);
    this.concatPatterns(this.patternDictionary);
    this.addSymbol(this.patternDictionary);
    this.sort(this.defaultPattens, this.patternDictionary);
    this.isInitialized = true;
  }

  static async createBaseList() {
    this.addLittle_tsu(this.patternDictionary);
  }

  static addLittle_tsu(list) {
    for (const key of Object.keys(list)) {
      // 「あ、え、な行、っ」の全パターンは「っ」を前に連結できないので省く
      if (key.match(/^[^あえおな-のっ]/) !== null) {
        list['っ' + key] = []; for (const value of list[key]) {
          //  「い、う、ん」の一部パターンは「っ」を前に連結できないので省く
          if (value.match(/(^I$|^U$|^NN$|^N'$)/) === null) {
            list['っ' + key].push(value[0] + value);
          }
        }
      }
    }
  }

  static concatPatterns(list) {
    for (const key of Object.keys(list)) {
      if (key.length == 2) {
        list[key] = list[key].concat(this.concatTwo(list[key[0]], list[key[1]]));
      }
      else if (key.length == 3) {
        list[key] = list[key]
          .concat(this.concatTwo(list[key[0]], list[key[1] + key[2]]));
        for (const firstchar of list[key[0]]) {
          for (const secondchar of list[key[1]]) {
            for (const thirdchar of list[key[2]]) {
              list[key].push(firstchar + secondchar + thirdchar);
            }
          }
        }
      }
    }
  }

  static concatTwo(first, second) {
    const result = [];
    for (const firstchar of first) {
      for (const secondchar of second) { result.push(firstchar + secondchar); }
    }
    return result;
  }

  static sort(target, patterns) {
    let tmp;
    for (const key of Object.keys(patterns)) {
      if (target[key] !== undefined && target[key] !== patterns[key][0]) {
        tmp = patterns[key][0]; patterns[key][0] = target[key];
        patterns[key][patterns[key].lastIndexOf(target[key])] = tmp;
      }
    }
  }

  static addSymbol(list) {
    const SYMBOL_LIST = [',', '.', '/', '-', '!', '?', '[', ']', '(', ')', '~'];
    for (let i = 0; i < 10; i++) list[i.toString()] = [i.toString()];
    for (const symbol of SYMBOL_LIST) list[symbol] = [symbol];
  }

  static containsKey(target) {
    return this.patternDictionary[target] !== undefined;
  }
}
