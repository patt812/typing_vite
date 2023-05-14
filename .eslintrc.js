module.exports = {
  env: {
    browser: true,
    es2021: true,
  },
  extends: [
    'plugin:vue/vue3-recommended',
    'airbnb-base',
  ],
  globals: {
    // Inertia等の組み込み関数をエラーにしない
    route: true,
    axios: true,
  },
  overrides: [],
  parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module',
  },
  plugins: [
    'vue',
    'unused-imports',
  ],
  rules: {
    'unused-imports/no-unused-imports': 'error',
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
};
