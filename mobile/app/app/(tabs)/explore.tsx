import { useEffect } from 'react';
import { StyleSheet, FlatList, TouchableOpacity, View, ImageBackground } from 'react-native';
import { useRouter } from 'expo-router';
import { Image } from 'expo-image';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { useRooms } from '@/hooks/use-rooms';
import { Palette as Colors, Spacing, Fonts } from '@/constants/theme';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

export default function ExploreScreen() {
  const { rooms, rentals, loading, fetchRooms } = useRooms();
  const router = useRouter();
  const insets = useSafeAreaInsets();

  useEffect(() => {
    fetchRooms();
  }, [fetchRooms]);

  const renderItem = ({ item, type }: { item: any, type: 'room' | 'exclusive' }) => (
    <TouchableOpacity
      style={styles.card}
      onPress={() => router.push({ pathname: '/room/[id]', params: { id: item.id, type } })}
    >
      <Image
        source={item.image ? { uri: item.image } : require('@/assets/images/reservation/service-option1.png')}
        style={styles.cardImage}
        contentFit="cover"
      />
      <View style={styles.cardContent}>
        <ThemedText type="subtitle" style={styles.cardTitle}>{item.name}</ThemedText>
        <ThemedText style={styles.cardPrice}>
          {type === 'room'
            ? (typeof item.base_price_weekday === 'number'
                ? `₱${item.base_price_weekday.toLocaleString()}/night`
                : `Price varies`)
            : (typeof item.price_range_min === 'number' && typeof item.price_range_max === 'number'
                ? `₱${item.price_range_min.toLocaleString()} - ₱${item.price_range_max.toLocaleString()}`
                : `Price varies`)}
        </ThemedText>
        {type === 'room' && (
          <View style={styles.badgeContainer}>
            <View style={styles.badge}>
              <ThemedText style={styles.badgeText}>
                {`${item.min_pax ?? '—'}-${item.max_pax ?? '—'} Pax`}
              </ThemedText>
            </View>
          </View>
        )}
      </View>
    </TouchableOpacity>
  );

  return (
    <ThemedView style={[styles.container, { paddingTop: insets.top }]}>
      <View style={styles.header}>
        <ThemedText type="title" style={{ fontFamily: Fonts.rounded }}>Explore Stays</ThemedText>
        <ThemedText style={styles.subtitle}>Find your perfect getaway</ThemedText>
      </View>

      <FlatList
        data={[...rooms.map(r => ({ ...r, type: 'room' })), ...rentals.map(r => ({ ...r, type: 'exclusive' }))]}
        keyExtractor={(item) => `${item.type}-${item.id}`}
        renderItem={({ item }) => renderItem({ item, type: item.type as any })}
        contentContainerStyle={styles.listContent}
        refreshing={loading}
        onRefresh={fetchRooms}
      />
    </ThemedView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  header: {
    paddingHorizontal: Spacing.lg,
    paddingBottom: Spacing.md,
  },
  subtitle: {
    color: Colors.gray,
    marginTop: Spacing.xs,
  },
  listContent: {
    padding: Spacing.lg,
    gap: Spacing.lg,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 3,
    marginBottom: Spacing.sm,
  },
  cardImage: {
    width: '100%',
    height: 200,
  },
  cardContent: {
    padding: Spacing.md,
  },
  cardTitle: {
    fontFamily: Fonts.rounded,
    fontSize: 18,
    marginBottom: Spacing.xs,
  },
  cardPrice: {
    color: Colors.primary,
    fontWeight: 'bold',
    fontSize: 16,
    marginBottom: Spacing.sm,
  },
  badgeContainer: {
    flexDirection: 'row',
  },
  badge: {
    backgroundColor: '#f0f0f0',
    paddingHorizontal: Spacing.sm,
    paddingVertical: 4,
    borderRadius: 8,
  },
  badgeText: {
    fontSize: 12,
    color: Colors.gray,
  },
});
