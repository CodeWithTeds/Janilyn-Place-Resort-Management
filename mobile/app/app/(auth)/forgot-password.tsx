import React, { useState } from 'react';
import { View, StyleSheet, Text, Alert } from 'react-native';
import { Link } from 'expo-router';
import { Input } from '../../components/ui/Input';
import { Button } from '../../components/ui/Button';
import { Colors } from '../../constants/theme';
import { useColorScheme } from '../../hooks/use-color-scheme';

export default function ForgotPasswordScreen() {
  const colorScheme = useColorScheme();
  const theme = Colors[colorScheme ?? 'light'];

  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);

  const handleReset = async () => {
    if (!email) {
      Alert.alert('Error', 'Please enter your email');
      return;
    }

    setLoading(true);
    // TODO: Implement forgot password API call
    setTimeout(() => {
      setLoading(false);
      Alert.alert('Success', 'If an account exists, a reset link has been sent.');
    }, 1500);
  };

  return (
    <View style={[styles.container, { backgroundColor: theme.background }]}>
      <View style={styles.header}>
        <Text style={[styles.title, { color: theme.text }]}>Forgot Password</Text>
        <Text style={[styles.subtitle, { color: theme.icon }]}>Enter your email to reset password</Text>
      </View>

      <View style={styles.form}>
        <Input
          label="Email"
          placeholder="Enter your email"
          value={email}
          onChangeText={setEmail}
          autoCapitalize="none"
          keyboardType="email-address"
        />

        <Button
          title="Send Reset Link"
          onPress={handleReset}
          loading={loading}
          style={styles.button}
        />

        <View style={styles.footer}>
          <Link href={"/(auth)/login" as any} asChild>
            <Text style={{ color: theme.primary, fontWeight: 'bold' }}>Back to Sign In</Text>
          </Link>
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 24,
    justifyContent: 'center',
  },
  header: {
    marginBottom: 32,
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 16,
  },
  form: {
    gap: 16,
  },
  button: {
    marginTop: 8,
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 16,
  },
});
