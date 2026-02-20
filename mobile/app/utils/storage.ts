import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

/**
 * A storage utility that wraps SecureStore for native platforms
 * and localStorage for web, ensuring cross-platform compatibility.
 */
export const storage = {
  /**
   * Save a value to storage
   */
  async setItem(key: string, value: string): Promise<void> {
    if (Platform.OS === 'web') {
      try {
        if (typeof localStorage !== 'undefined') {
          localStorage.setItem(key, value);
        }
      } catch (e) {
        console.error('Local storage is unavailable:', e);
      }
    } else {
      await SecureStore.setItemAsync(key, value);
    }
  },

  /**
   * Retrieve a value from storage
   */
  async getItem(key: string): Promise<string | null> {
    if (Platform.OS === 'web') {
      try {
        if (typeof localStorage !== 'undefined') {
          return localStorage.getItem(key);
        }
        return null;
      } catch (e) {
        console.error('Local storage is unavailable:', e);
        return null;
      }
    } else {
      return await SecureStore.getItemAsync(key);
    }
  },

  /**
   * Remove a value from storage
   */
  async removeItem(key: string): Promise<void> {
    if (Platform.OS === 'web') {
      try {
        if (typeof localStorage !== 'undefined') {
          localStorage.removeItem(key);
        }
      } catch (e) {
        console.error('Local storage is unavailable:', e);
      }
    } else {
      await SecureStore.deleteItemAsync(key);
    }
  }
};
