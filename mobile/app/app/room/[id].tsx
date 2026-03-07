import { useLocalSearchParams, useRouter } from 'expo-router';
import { StyleSheet, ScrollView, View, Alert } from 'react-native';
import { Image } from 'expo-image';
import { useEffect, useState } from 'react';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { RoomService } from '@/services/room.service';
import { RoomType, ExclusiveResortRental } from '@/types/room';
import { Palette as Colors, Spacing, Fonts } from '@/constants/theme';
import { Button } from '@/components/ui/Button';

export default function RoomDetailScreen() {
  const { id, type } = useLocalSearchParams<{ id: string; type: 'room' | 'exclusive' }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();

  const [item, setItem] = useState<RoomType | ExclusiveResortRental | null>(null);
  const [loading, setLoading] = useState(true);

  const formatPHP = (value: unknown) => {
    const n = Number(value);
    return Number.isFinite(n) ? `₱${n.toLocaleString('en-PH')}` : '₱—';
  };

  useEffect(() => {
    const fetchDetails = async () => {
      try {
        if (type === 'room') {
          const data = await RoomService.getRoom(Number(id));
          setItem(data);
        } else {
          const data = await RoomService.getRental(Number(id));
          setItem(data);
        }
      } catch (error) {
        Alert.alert('Error', 'Failed to load details');
        router.back();
      } finally {
        setLoading(false);
      }
    };

    if (id) fetchDetails();
  }, [id, type]);

  if (loading || !item) {
    return (
      <ThemedView style={styles.loadingContainer}>
        <ThemedText>Loading...</ThemedText>
      </ThemedView>
    );
  }

  const isRoom = type === 'room';
  // Type guards or casting
  const roomItem = isRoom ? (item as RoomType) : null;
  const rentalItem = !isRoom ? (item as ExclusiveResortRental) : null;

  const priceText = isRoom 
    ? `${formatPHP(roomItem?.base_price_weekday)}/night`
    : `${formatPHP(rentalItem?.price_range_min)} - ${formatPHP(rentalItem?.price_range_max)}`;

  const capacityText = isRoom
    ? `${roomItem?.min_pax ?? '—'}-${roomItem?.max_pax ?? '—'} Pax`
    : `${rentalItem?.capacity_overnight_min ?? '—'}-${rentalItem?.capacity_overnight_max ?? '—'} Pax`;

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={{ paddingBottom: 100 }}>
        <Image
          source={item.image ? { uri: item.image } : require('@/assets/images/reservation/service-option1.png')}
          style={styles.image}
          contentFit="cover"
        />
        
        <View style={styles.content}>
          <ThemedText type="title" style={styles.title}>{item.name}</ThemedText>
          <ThemedText style={styles.price}>{priceText}</ThemedText>
          
          <View style={styles.section}>
            <ThemedText type="subtitle" style={styles.sectionTitle}>Description</ThemedText>
            <ThemedText style={styles.description}>{item.description}</ThemedText>
          </View>

          <View style={styles.section}>
            <ThemedText type="subtitle" style={styles.sectionTitle}>Capacity</ThemedText>
            <ThemedText style={styles.text}>{capacityText}</ThemedText>
          </View>

          {isRoom && (
             <View style={styles.section}>
               <ThemedText type="subtitle" style={styles.sectionTitle}>Pricing</ThemedText>
               <ThemedText style={styles.text}>Weekday: {formatPHP(roomItem?.base_price_weekday)}</ThemedText>
               <ThemedText style={styles.text}>Weekend: {formatPHP(roomItem?.base_price_weekend)}</ThemedText>
               <ThemedText style={styles.text}>Extra Person: {formatPHP(roomItem?.extra_person_charge)}</ThemedText>
             </View>
          )}
        </View>
      </ScrollView>

      <View style={[styles.footer, { paddingBottom: insets.bottom + Spacing.md }]}>
        <Button 
          title="Book Now" 
          onPress={() => router.push({ pathname: '/booking/create', params: { id, type } })} 
        />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  image: {
    width: '100%',
    height: 300,
  },
  content: {
    padding: Spacing.lg,
  },
  title: {
    fontFamily: Fonts.rounded,
    fontSize: 24,
    marginBottom: Spacing.xs,
  },
  price: {
    color: Colors.primary,
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: Spacing.lg,
  },
  section: {
    marginBottom: Spacing.lg,
  },
  sectionTitle: {
    marginBottom: Spacing.xs,
    fontSize: 18,
  },
  description: {
    color: Colors.gray,
    lineHeight: 24,
  },
  text: {
    color: Colors.gray,
    fontSize: 16,
    marginBottom: 4,
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: '#fff',
    padding: Spacing.lg,
    borderTopWidth: 1,
    borderTopColor: '#f0f0f0',
  },
});
