module.exports = {
  env: {
    browser: true,
    es2021: true,
  },
  extends: ['plugin:vue/vue3-recommended', 'airbnb-base', 'prettier'],
  globals: {
    route: true,
    axios: true,
  },
  parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module',
  },
  plugins: ['vue', 'unused-imports'],
  rules: {
    'import/no-extraneous-dependencies': 'off',
    'unused-imports/no-unused-imports': 'error',
    'no-continue': 'off',
    'no-restricted-syntax': 'off',
    'vue/max-attributes-per-line': ['error', { singleline: 6, multiline: { max: 16 } }],
    'unused-imports/no-unused-vars': [
      'off',
      {
        vars: 'all',
        varsIgnorePattern: '^_',
        args: 'after-used',
        argsIgnorePattern: '^_',
      },
    ],
  },
  settings: {
    'import/resolver': {
      alias: {
        map: [['@', './resources/js/']],
      },
    },
  },
};
