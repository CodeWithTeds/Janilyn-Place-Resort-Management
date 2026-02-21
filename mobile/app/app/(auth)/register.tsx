import React, { useState } from 'react';
import { View, StyleSheet, Text, Alert, Image, KeyboardAvoidingView, Platform, ScrollView, TouchableOpacity } from 'react-native';
import { Link, useRouter } from 'expo-router';
import { useAuth } from '../../hooks/useAuth';
import { Input } from '../../components/ui/Input';
import { Button } from '../../components/ui/Button';
import { Colors } from '../../constants/theme';
import { useColorScheme } from '../../hooks/use-color-scheme';

export default function RegisterScreen() {
  const { signUp } = useAuth();
  const router = useRouter();
  const colorScheme = useColorScheme();
  const theme = Colors[colorScheme ?? 'light'];

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [nameError, setNameError] = useState('');
  const [emailError, setEmailError] = useState('');
  const [passwordError, setPasswordError] = useState('');
  const [confirmPasswordError, setConfirmPasswordError] = useState('');
  const [loading, setLoading] = useState(false);

  const validateEmail = (email: string) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  const clearErrors = () => {
    setNameError('');
    setEmailError('');
    setPasswordError('');
    setConfirmPasswordError('');
  };

  const handleRegister = async () => {
    clearErrors();

    let hasError = false;

    if (!name) {
      setNameError('Name is required');
      hasError = true;
    }

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
    } else if (password.length < 8) {
      setPasswordError('Password must be at least 8 characters');
      hasError = true;
    }

    if (!confirmPassword) {
      setConfirmPasswordError('Confirm Password is required');
      hasError = true;
    } else if (password !== confirmPassword) {
      setConfirmPasswordError('Passwords do not match');
      hasError = true;
    }

    if (hasError) return;

    setLoading(true);
    try {
      await signUp({
        name,
        email,
        password,
        password_confirmation: confirmPassword,
      });
    } catch (error: any) {
      const errors = error.response?.data?.errors;
      
      if (errors) {
        if (errors.name) setNameError(errors.name[0]);
        if (errors.email) setEmailError(errors.email[0]);
        if (errors.password) setPasswordError(errors.password[0]);
      } else {
         Alert.alert('Registration Failed', error.response?.data?.message || 'Something went wrong');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={[styles.container, { backgroundColor: theme.background }]}
    >
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.header}>
          <Image
            source={require('../../assets/images/logo.png')}
            style={styles.logo}
            resizeMode="contain"
          />
          <Text style={[styles.appName, { color: theme.primary }]}>Janilyn's Place</Text>
          <Text style={[styles.title, { color: theme.text }]}>Create Account</Text>
          <Text style={[styles.subtitle, { color: theme.icon }]}>Sign up to get started</Text>
        </View>

        <View style={[
          styles.card, 
          { 
            backgroundColor: colorScheme === 'dark' ? '#1e293b' : '#fff',
            borderColor: colorScheme === 'dark' ? '#334155' : '#e2e8f0',
          }
        ]}>
          <Input
            label="Full Name"
            placeholder="Enter your full name"
            value={name}
            onChangeText={(text) => {
              setName(text);
              if (nameError) setNameError('');
            }}
            error={nameError}
          />

          <View style={styles.inputContainer}>
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
          </View>

          <View style={styles.inputContainer}>
            <Input
              label="Password"
              placeholder="Create a password"
              value={password}
              onChangeText={(text) => {
                setPassword(text);
                if (passwordError) setPasswordError('');
              }}
              secureTextEntry
              error={passwordError}
            />
          </View>

          <View style={styles.inputContainer}>
            <Input
              label="Confirm Password"
              placeholder="Confirm your password"
              value={confirmPassword}
              onChangeText={(text) => {
                setConfirmPassword(text);
                if (confirmPasswordError) setConfirmPasswordError('');
              }}
              secureTextEntry
              error={confirmPasswordError}
            />
          </View>

          <Button
            title="Sign Up"
            onPress={handleRegister}
            loading={loading}
            style={styles.signUpButton}
          />
        </View>

        <View style={styles.footer}>
          <Text style={[styles.footerText, { color: theme.icon }]}>Already have an account?</Text>
          <Link href="/(auth)/login" asChild>
            <TouchableOpacity>
              <Text style={[styles.footerLink, { color: theme.primary }]}>Sign In</Text>
            </TouchableOpacity>
          </Link>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
    padding: 24,
    justifyContent: 'center',
  },
  header: {
    alignItems: 'center',
    marginBottom: 32,
  },
  logo: {
    width: 80,
    height: 80,
    marginBottom: 16,
    borderRadius: 16,
  },
  appName: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 8,
  },
  title: {
    fontSize: 20,
    fontWeight: '600',
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 16,
  },
  card: {
    borderRadius: 16,
    padding: 24,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 2,
    borderWidth: 1,
  },
  inputContainer: {
    marginTop: 16,
  },
  signUpButton: {
    marginTop: 24,
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 32,
    gap: 8,
    marginBottom: 20,
  },
  footerText: {
    fontSize: 14,
  },
  footerLink: {
    fontSize: 14,
    fontWeight: '600',
  },
});