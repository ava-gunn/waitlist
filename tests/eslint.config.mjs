import jestPlugin from 'eslint-plugin-jest';
import jestDomPlugin from 'eslint-plugin-jest-dom';
import testingLibraryPlugin from 'eslint-plugin-testing-library';

export default [
  {
    files: ['**/*.test.{ts,tsx,js,jsx}'],
    plugins: {
      'jest': jestPlugin,
      'jest-dom': jestDomPlugin,
      'testing-library': testingLibraryPlugin,
    },
    languageOptions: {
      globals: {
        'jest/globals': true,
      },
    },
    rules: {
      // Ensure proper accessibility testing
      'testing-library/prefer-screen-queries': 'error',
      'testing-library/prefer-presence-queries': 'error',
      'testing-library/prefer-role-queries': 'warn',
      'testing-library/no-wait-for-multiple-assertions': 'warn',
      'testing-library/no-render-in-setup': 'error',
      'jest/expect-expect': 'error',
      'jest/no-disabled-tests': 'warn',
      'jest/no-focused-tests': 'error',
      'jest/valid-expect': 'error',
      'jest-dom/prefer-checked': 'error',
      'jest-dom/prefer-enabled-disabled': 'error',
      'jest-dom/prefer-in-document': 'error',
      'jest-dom/prefer-to-have-attribute': 'error',
      'jest-dom/prefer-to-have-class': 'error',
      'jest-dom/prefer-to-have-style': 'error',
      'jest-dom/prefer-to-have-text-content': 'error',
      // Disable type checking rules for tests
      '@typescript-eslint/no-explicit-any': 'off',
    },
  }
];
