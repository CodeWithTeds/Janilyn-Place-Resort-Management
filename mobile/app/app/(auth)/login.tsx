import React, { useState } from 'react';
import { View, StyleSheet, Text, Alert } from 'react-native';
import { useRouter, Link } from 'expo-router';
import { useAuth } from '../../hooks/useAuth';
import { Input } from '../../components/ui/Input';
import { Button } from '../../components/ui/Button';
import { Colors } from '../../constants/theme';
import { useColorScheme } from '../../hooks/use-color-scheme';

export default function LoginScreen() {
  const router = useRouter();
  const { signIn } = useAuth();
  const colorScheme = useColorScheme();
  const theme = Colors[colorScheme ?? 'light'];

  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [emailError, setEmailError] = useState('');
  const [passwordError, setPasswordError] = useState('');
  const [loading, setLoading] = useState(false);

  const validateEmail = (email: string) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  const handleLogin = async () => {
    setEmailError('');
    setPasswordError('');

    let hasError = false;

    if (!email) {
      setEmailError('Email is required');
      hasError = true;
    } else if (!validateEmail(email)) {
      setEmailError('Please enter a valid email address');
      hasError = true;
    }

    if (!password) {
      setPasswordError('Password is required');
      hasError = true;
    }

    if (hasError) return;

    setLoading(true);
    try {
      await signIn({ email, password });
    } catch (error: any) {
      console.error('Login Error Full:', JSON.stringify(error.response?.data, null, 2));
      const errors = error.response?.data?.errors;
      
      if (errors) {
        if (errors.email) setEmailError(errors.email[0]);
        if (errors.password) setPasswordError(errors.password[0]);
      } else {
        Alert.alert('Login Failed', error.response?.data?.message || 'Something went wrong');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={[styles.container, { backgroundColor: theme.background }]}>
      <View style={styles.header}>
        <Text style={[styles.title, { color: theme.text }]}>Welcome Back</Text>
        <Text style={[styles.subtitle, { color: theme.icon }]}>Sign in to continue</Text>
      </View>

      <View style={styles.form}>
        <Input
          label="Email"
          placeholder="Enter your email"
          value={email}
          onChangeText={(text) => {
            setEmail(text);
            if (emailError) setEmailError('');
          }}
          autoCapitalize="none"
          keyboardType="email-address"
          error={emailError}
        />
        <Input
          label="Password"
          placeholder="Enter your password"
          value={password}
          onChangeText={(text) => {
            setPassword(text);
            if (passwordError) setPasswordError('');
          }}
          secureTextEntry
          error={passwordError}
        />

        <Button
          title="Sign In"
          onPress={handleLogin}
          loading={loading}
          style={styles.button}
        />

        <View style={styles.footer}>
          <Text style={{ color: theme.text }}>Don't have an account? </Text>
          <Link href="/(auth)/register" style={{ color: theme.primary, fontWeight: 'bold' }}>
            Sign Up
          </Link>
        </View>
        
        <View style={styles.forgotPassword}>
           <Link href="/(auth)/forgot-password" style={{ color: theme.primary }}>
            Forgot Password?
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
  forgotPassword: {
    alignItems: 'center',
    marginTop: 16,
  }
});
