import romaPatterns from './romaPatterns';

export default class Patterns {
  static patternDictionary = romaPatterns.patternDictionary;

  static defaultPattens = romaPatterns.defaultPattens;

  static isInitialized = false;

  static async initialize() {
    if (this.isInitialized) return;
    this.patternDictionary = this.addLittle_tsu(this.patternDictionary);
    this.patternDictionary = this.concatPatterns(this.patternDictionary);
    this.patternDictionary = this.addSymbol(this.patternDictionary);
    this.sort(this.defaultPattens, this.patternDictionary);
    this.isInitialized = true;
  }

  static async createBaseList() {
    this.addLittle_tsu(this.patternDictionary);
  }

  static addLittle_tsu(list) {
    const newList = { ...list };

    for (const key of Object.keys(newList)) {
      // 「あ、え、な行、っ」の全パターンは「っ」を前に連結できないので省く
      if (key.match(/^[^あえおな-のっ]/) !== null) {
        newList[`っ${key}`] = [];
        for (const value of newList[key]) {
          //  「い、う、ん」の一部パターンは「っ」を前に連結できないので省く
          if (value.match(/(^I$|^U$|^NN$|^N'$)/) === null) {
            newList[`っ${key}`].push(value[0] + value);
          }
        }
      }
    }
    return newList;
  }

  static concatPatterns(list) {
    const newList = { ...list };

    for (const key of Object.keys(newList)) {
      if (key.length === 2) {
        newList[key] = newList[key].concat(this.concatTwo(newList[key[0]], newList[key[1]]));
      } else if (key.length === 3) {
        newList[key] = newList[key]
          .concat(this.concatTwo(newList[key[0]], newList[key[1] + key[2]]));
        for (const firstchar of newList[key[0]]) {
          for (const secondchar of newList[key[1]]) {
            for (const thirdchar of newList[key[2]]) {
              newList[key].push(firstchar + secondchar + thirdchar);
            }
          }
        }
      }
    }
    return newList;
  }

  static concatTwo(first, second) {
    const result = [];
    for (const firstchar of first) {
      for (const secondchar of second) { result.push(firstchar + secondchar); }
    }
    return result;
  }

  static sort(target, patterns) {
    const newPatterns = { ...patterns };

    for (const key of Object.keys(newPatterns)) {
      if (target[key] !== undefined && target[key] !== newPatterns[key][0]) {
        const [tmp] = newPatterns[key];
        newPatterns[key][0] = target[key];
        newPatterns[key][newPatterns[key].lastIndexOf(target[key])] = tmp;
      }
    }

    return newPatterns;
  }

  static addSymbol(list) {
    const newList = { ...list };
    const SYMBOL_LIST = [',', '.', '/', '-', '!', '?', '[', ']', '(', ')', '~'];
    for (let i = 0; i < 10; i += 1) newList[i.toString()] = [i.toString()];
    for (const symbol of SYMBOL_LIST) newList[symbol] = [symbol];
    newList['ー'] = ['-'];
    return newList;
  }

  static containsKey(target) {
    return this.patternDictionary[target] !== undefined;
  }
}
