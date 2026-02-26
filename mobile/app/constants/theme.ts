/**
 * Below are the colors that are used in the app. The colors are defined in the light and dark mode.
 * There are many other ways to style your app. For example, [Nativewind](https://www.nativewind.dev/), [Tamagui](https://tamagui.dev/), [unistyles](https://reactnativeunistyles.vercel.app), etc.
 */

import { Platform } from 'react-native';

const tintColorLight = '#0284c7'; // Sky 600
const tintColorDark = '#fff';

export const Colors = {
  light: {
    text: '#0f172a', // Slate 900
    background: '#f0f9ff', // Sky 50
    tint: tintColorLight,
    icon: '#64748b', // Slate 500
    tabIconDefault: '#64748b',
    tabIconSelected: tintColorLight,
    primary: '#0284c7', // Sky 600
    secondary: '#0ea5e9', // Sky 500
    accent: '#e0f2fe', // Sky 100
    backgroundDark: '#0f172a',
    gray: '#64748b', // Added for compatibility
  },
  dark: {
    text: '#f1f5f9', // Slate 100
    background: '#0f172a', // Slate 900
    tint: tintColorDark,
    icon: '#94a3b8', // Slate 400
    tabIconDefault: '#94a3b8',
    tabIconSelected: tintColorDark,
    primary: '#38bdf8', // Sky 400
    secondary: '#7dd3fc', // Sky 300
    accent: '#0c4a6e', // Sky 900
    backgroundDark: '#0f172a',
    gray: '#94a3b8', // Added for compatibility
  },
};

export const Spacing = {
  xs: 4,
  sm: 8,
  md: 12,
  lg: 16,
  xl: 24,
  xxl: 32,
};

export const Fonts = {
  sans: Platform.select({ ios: 'system-ui', default: 'sans-serif' }),
  serif: Platform.select({ ios: 'ui-serif', default: 'serif' }),
  rounded: Platform.select({ ios: 'ui-rounded', default: 'sans-serif-rounded' }),
  mono: Platform.select({ ios: 'ui-monospace', default: 'monospace' }),
};

// Helper for direct access if needed (prefer useColorScheme hook)
export const Palette = Colors.light;
