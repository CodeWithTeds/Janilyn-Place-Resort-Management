import { StyleSheet, View } from 'react-native';
import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/Button';

export default function HomeScreen() {
  const { user, signOut } = useAuth();

  return (
    <ThemedView style={styles.container}>
      <View style={styles.content}>
        <ThemedText type="title" style={styles.title}>
          Welcome, {user?.name || 'Client'}!
        </ThemedText>
        <ThemedText style={styles.subtitle}>
          You are now logged in.
        </ThemedText>
        
        <View style={styles.buttonContainer}>
          <Button 
            title="Log Out" 
            onPress={signOut}
            style={styles.button}
          />
        </View>
      </View>
    </ThemedView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    padding: 24,
  },
  content: {
    alignItems: 'center',
    gap: 16,
  },
  title: {
    marginBottom: 8,
    textAlign: 'center',
  },
  subtitle: {
    marginBottom: 32,
    textAlign: 'center',
    opacity: 0.7,
  },
  buttonContainer: {
    width: '100%',
    maxWidth: 300,
  },
  button: {
    backgroundColor: '#FF3B30', // Red color for logout
  },
});
