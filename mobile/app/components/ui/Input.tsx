import React from 'react';
import { TextInput, StyleSheet, View, Text, TextInputProps } from 'react-native';
import { Colors } from '../../constants/theme';
import { useColorScheme } from '../../hooks/use-color-scheme';

interface InputProps extends TextInputProps {
  label?: string;
  error?: string;
}

export function Input({ label, error, style, ...props }: InputProps) {
  const colorScheme = useColorScheme();
  const theme = Colors[colorScheme ?? 'light'];

  return (
    <View style={styles.container}>
      {!!label && <Text style={[styles.label, { color: theme.text }]}>{label}</Text>}
      <TextInput
        style={[
          styles.input,
          { 
            color: theme.text,
            borderColor: error ? '#EF4444' : theme.icon,
            backgroundColor: theme.background,
          },
          style,
        ]}
        placeholderTextColor={theme.icon}
        {...props}
      />
      {!!error && <Text style={styles.error}>{error}</Text>}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    marginBottom: 16,
  },
  label: {
    marginBottom: 8,
    fontSize: 14,
    fontWeight: '500',
  },
  input: {
    height: 50,
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 12,
    fontSize: 16,
  },
  error: {
    marginTop: 4,
    fontSize: 12,
    color: '#EF4444',
  },
});
