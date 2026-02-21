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
  },
};

export const Fonts = Platform.select({
  ios: {
    /** iOS `UIFontDescriptorSystemDesignDefault` */
    sans: 'system-ui',
    /** iOS `UIFontDescriptorSystemDesignSerif` */
    serif: 'ui-serif',
    /** iOS `UIFontDescriptorSystemDesignRounded` */
    rounded: 'ui-rounded',
    /** iOS `UIFontDescriptorSystemDesignMonospaced` */
    mono: 'ui-monospace',
  },
  default: {
    sans: 'normal',
    serif: 'serif',
    rounded: 'normal',
    mono: 'monospace',
  },
  web: {
    sans: "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
    serif: "Georgia, 'Times New Roman', serif",
    rounded: "'SF Pro Rounded', 'Hiragino Maru Gothic ProN', Meiryo, 'MS PGothic', sans-serif",
    mono: "SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace",
  },
});
