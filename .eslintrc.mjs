export default {
  root: true,
  extends: [
    'eslint:recommended',
    'plugin:@typescript-eslint/recommended',
    'plugin:react/recommended',
    'plugin:react-hooks/recommended',
    'plugin:jsx-a11y/recommended',
  ],
  parser: '@typescript-eslint/parser',
  plugins: ['@typescript-eslint', 'react', 'jsx-a11y'],
  rules: {
    'react/react-in-jsx-scope': 'off',
    'react/prop-types': 'off',
    '@typescript-eslint/no-explicit-any': 'warn',
    '@typescript-eslint/no-unused-vars': ['error', { 'argsIgnorePattern': '^_', 'varsIgnorePattern': '^_' }],
    'jsx-a11y/alt-text': 'error',
    'jsx-a11y/aria-role': 'error',
    'jsx-a11y/no-redundant-roles': 'error',
    'jsx-a11y/img-redundant-alt': 'error',
  },
  settings: {
    react: {
      version: 'detect',
    },
  },
  parserOptions: {
    ecmaVersion: 2021,
    sourceType: 'module',
    ecmaFeatures: {
      jsx: true,
    },
  },
  env: {
    browser: true,
    es2021: true,
    node: true,
  },
  ignorePatterns: ["**/node_modules/**", "**/vendor/**", "**/bootstrap/**", "**/public/**", "**/storage/**", "**/build/**", "**/dist/**"],
  overrides: [
    {
      files: ['**/*.test.{ts,tsx,js,jsx}', '**/tests/**/*.{ts,tsx,js,jsx}'],
      extends: [
        'plugin:jest/recommended',
        'plugin:jest-dom/recommended',
        'plugin:testing-library/react',
      ],
      env: {
        'jest/globals': true,
      },
      rules: {
        'testing-library/prefer-screen-queries': 'error',
        'testing-library/prefer-presence-queries': 'error', 
        'testing-library/prefer-role-queries': 'warn',
        'testing-library/no-wait-for-multiple-assertions': 'warn',
        'testing-library/no-render-in-setup': 'error',
        'jest/expect-expect': 'error',
        'jest/no-disabled-tests': 'warn',
        'jest/no-focused-tests': 'error',
        'jest/valid-expect': 'error',
        '@typescript-eslint/no-explicit-any': 'off',
      },
    },
  ],
};
