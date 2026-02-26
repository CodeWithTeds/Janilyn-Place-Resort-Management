import { useLocalSearchParams, useRouter } from 'expo-router';
import { StyleSheet, ScrollView, View, Alert, Platform } from 'react-native';
import { useState } from 'react';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import DateTimePicker from '@react-native-community/datetimepicker';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Colors as ThemeColors, Spacing, Fonts, Palette as Colors } from '@/constants/theme';
import { BookingService } from '@/services/booking.service';
import { RoomService } from '@/services/room.service';

export default function CreateBookingScreen() {
  const { id, type } = useLocalSearchParams<{ id: string; type: 'room' | 'exclusive' }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();

  const [checkIn, setCheckIn] = useState(new Date());
  const [checkOut, setCheckOut] = useState(new Date(new Date().setDate(new Date().getDate() + 1)));
  const [paxCount, setPaxCount] = useState('');
  const [loading, setLoading] = useState(false);
  
  const [showCheckIn, setShowCheckIn] = useState(false);
  const [showCheckOut, setShowCheckOut] = useState(false);

  const handleBooking = async () => {
    if (!paxCount) {
      Alert.alert('Error', 'Please enter number of guests');
      return;
    }

    try {
      setLoading(true);
      
      // 1. Check Availability (Optional but good UX)
      // For now we assume user checked dates or we just try to book.
      
      // 2. Create Booking
      const bookingData = {
        booking_type: type,
        [type === 'room' ? 'room_type_id' : 'exclusive_resort_rental_id']: Number(id),
        check_in: checkIn.toISOString().split('T')[0],
        check_out: checkOut.toISOString().split('T')[0],
        pax_count: Number(paxCount),
        payment_method: 'paymongo', // Hardcoded for this demo flow as requested
      };

      const response = await BookingService.create(bookingData as any);

      if (response.checkout_url) {
        // Redirect to payment
        router.push({
          pathname: '/booking/payment',
          params: { url: response.checkout_url, bookingId: response.booking.id }
        });
      } else {
        // Cash payment or no payment required (should not happen with paymongo)
        router.push('/booking/success');
      }

    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || 'Failed to create booking');
    } finally {
      setLoading(false);
    }
  };

  const onChangeCheckIn = (event: any, selectedDate?: Date) => {
    setShowCheckIn(Platform.OS === 'ios');
    if (selectedDate) {
      setCheckIn(selectedDate);
      // Ensure checkout is after checkin
      if (selectedDate >= checkOut) {
        const nextDay = new Date(selectedDate);
        nextDay.setDate(selectedDate.getDate() + 1);
        setCheckOut(nextDay);
      }
    }
  };

  const onChangeCheckOut = (event: any, selectedDate?: Date) => {
    setShowCheckOut(Platform.OS === 'ios');
    if (selectedDate) {
      setCheckOut(selectedDate);
    }
  };

  return (
    <ThemedView style={[styles.container, { paddingTop: insets.top }]}>
      <View style={styles.header}>
        <ThemedText type="title" style={{ fontFamily: Fonts.rounded }}>Book Your Stay</ThemedText>
      </View>

      <ScrollView contentContainerStyle={styles.content}>
        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.label}>Check-in Date</ThemedText>
          <Button 
            variant="outline" 
            title={checkIn.toLocaleDateString()} 
            onPress={() => setShowCheckIn(true)} 
          />
          {showCheckIn && (
            <DateTimePicker
              value={checkIn}
              mode="date"
              display="default"
              minimumDate={new Date()}
              onChange={onChangeCheckIn}
            />
          )}
        </View>

        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.label}>Check-out Date</ThemedText>
          <Button 
            variant="outline" 
            title={checkOut.toLocaleDateString()} 
            onPress={() => setShowCheckOut(true)} 
          />
          {showCheckOut && (
            <DateTimePicker
              value={checkOut}
              mode="date"
              display="default"
              minimumDate={checkIn}
              onChange={onChangeCheckOut}
            />
          )}
        </View>

        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.label}>Number of Guests</ThemedText>
          <Input
            value={paxCount}
            onChangeText={setPaxCount}
            placeholder="e.g. 2"
            keyboardType="numeric"
          />
        </View>

        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.label}>Payment Method</ThemedText>
          <View style={styles.paymentCard}>
            <ThemedText style={styles.paymentText}>PayMongo (Card/GCash)</ThemedText>
            <ThemedText style={styles.paymentSubtext}>Secure payment via PayMongo</ThemedText>
          </View>
        </View>

      </ScrollView>

      <View style={[styles.footer, { paddingBottom: insets.bottom + Spacing.md }]}>
        <Button 
          title={loading ? "Processing..." : "Proceed to Payment"} 
          onPress={handleBooking} 
          disabled={loading}
        />
      </View>
    </ThemedView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  header: {
    padding: Spacing.lg,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  content: {
    padding: Spacing.lg,
  },
  section: {
    marginBottom: Spacing.xl,
  },
  label: {
    marginBottom: Spacing.sm,
    fontSize: 16,
  },
  paymentCard: {
    padding: Spacing.md,
    borderWidth: 1,
    borderColor: Colors.primary,
    borderRadius: 8,
    backgroundColor: '#f0f9ff',
  },
  paymentText: {
    color: Colors.primary,
    fontWeight: 'bold',
    fontSize: 16,
  },
  paymentSubtext: {
    color: Colors.gray,
    fontSize: 12,
    marginTop: 4,
  },
  footer: {
    padding: Spacing.lg,
    borderTopWidth: 1,
    borderTopColor: '#f0f0f0',
  },
});
