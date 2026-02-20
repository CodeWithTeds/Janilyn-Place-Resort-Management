import React from 'react';
import { TouchableOpacity, Text, StyleSheet, ActivityIndicator, TouchableOpacityProps } from 'react-native';
import { Colors } from '../../constants/theme';
import { useColorScheme } from '../../hooks/use-color-scheme';

interface ButtonProps extends TouchableOpacityProps {
  title: string;
  loading?: boolean;
  variant?: 'primary' | 'secondary' | 'outline';
}

export function Button({ title, loading, variant = 'primary', style, ...props }: ButtonProps) {
  const colorScheme = useColorScheme();
  const theme = Colors[colorScheme ?? 'light'];

  const getBackgroundColor = () => {
    if (variant === 'primary') return theme.primary;
    if (variant === 'secondary') return theme.secondary;
    return 'transparent';
  };

  const getTextColor = () => {
    if (variant === 'outline') return theme.primary;
    return '#fff';
  };

  const getBorderColor = () => {
    if (variant === 'outline') return theme.primary;
    return 'transparent';
  };

  return (
    <TouchableOpacity
      style={[
        styles.container,
        { backgroundColor: getBackgroundColor(), borderColor: getBorderColor() },
        variant === 'outline' && styles.outline,
        style,
      ]}
      disabled={loading || props.disabled}
      {...props}
    >
      {loading ? (
        <ActivityIndicator color={getTextColor()} />
      ) : (
        <Text style={[styles.text, { color: getTextColor() }]}>{title}</Text>
      )}
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  container: {
    height: 50,
    borderRadius: 8,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 16,
    flexDirection: 'row',
  },
  outline: {
    borderWidth: 1,
  },
  text: {
    fontSize: 16,
    fontWeight: '600',
  },
});
