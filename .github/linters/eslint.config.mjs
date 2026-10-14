// Flat ESLint configuration for Super-Linter v9 and modern local ESLint releases.
export default [
  {
    files: ['**/*.js'],
    languageOptions: {
      ecmaVersion: 2021,
      sourceType: 'script',
      globals: {
        document: 'readonly',
        window: 'readonly'
      }
    },
    rules: {
      'no-constant-condition': 'error',
      'no-dupe-keys': 'error',
      'no-undef': 'error',
      'no-unreachable': 'error',
      'no-unused-vars': 'error'
    }
  }
];
