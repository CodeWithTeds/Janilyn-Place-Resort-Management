import { useLocalSearchParams, useRouter } from 'expo-router';
import { StyleSheet, ScrollView, View, Alert, Platform, TouchableOpacity, Text, Image } from 'react-native';
import { useState, useEffect } from 'react';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import DateTimePicker from '@react-native-community/datetimepicker';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Colors as ThemeColors, Spacing, Fonts, Palette as Colors } from '@/constants/theme';
import { BookingService } from '@/services/booking.service';
import { RoomService } from '@/services/room.service';
import { RoomType, ExclusiveResortRental } from '@/types/room';
import { useRooms } from '@/hooks/use-rooms';

interface Unit {
  id: number;
  name: string;
}

interface Tier {
  id: number;
  min_guests: number;
  max_guests: number;
  price_weekday: string;
  price_weekend: string;
  resort_unit_id: number | null;
}

export default function CreateBookingScreen() {
  const { id, type } = useLocalSearchParams<{ id: string; type: 'room' | 'exclusive' }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const { rooms, rentals, fetchRooms } = useRooms();

  const [checkIn, setCheckIn] = useState(new Date());
  const [checkOut, setCheckOut] = useState(new Date(new Date().setDate(new Date().getDate() + 1)));
  const [paxCount, setPaxCount] = useState('');
  const [loading, setLoading] = useState(false);
  
  const [showCheckIn, setShowCheckIn] = useState(false);
  const [showCheckOut, setShowCheckOut] = useState(false);

  // Room Type Selection State
  const [selectedRoomTypeId, setSelectedRoomTypeId] = useState<number | null>(id ? Number(id) : null);

  // New state for tiered pricing
  const [availableUnits, setAvailableUnits] = useState<Unit[]>([]);
  const [selectedUnitId, setSelectedUnitId] = useState<number | null>(null);
  const [availableTiers, setAvailableTiers] = useState<Tier[]>([]);
  const [selectedTierId, setSelectedTierId] = useState<number | null>(null);
  const [allTiers, setAllTiers] = useState<Tier[]>([]);
  const [roomType, setRoomType] = useState<RoomType | null>(null);
  const [rental, setRental] = useState<ExclusiveResortRental | null>(null);
  const [estimatedPrice, setEstimatedPrice] = useState(0);

  useEffect(() => {
    fetchRooms();
  }, [fetchRooms]);

  // Force re-render/update when dependencies change
  useEffect(() => {
    const price = calculateTotalPrice();
    setEstimatedPrice(price);
  }, [checkIn, checkOut, paxCount, selectedTierId, availableTiers, roomType, rental, type]);

  useEffect(() => {
    if (type === 'room' && selectedRoomTypeId) {
      loadRoomDetails(selectedRoomTypeId);
    }
  }, [selectedRoomTypeId, type]);

  useEffect(() => {
    if (type === 'room' && selectedRoomTypeId && checkIn && checkOut) {
      fetchUnits(selectedRoomTypeId);
    }
  }, [checkIn, checkOut, selectedRoomTypeId, type]);

  useEffect(() => {
    if (type === 'exclusive' && id) {
      loadRentalDetails(Number(id));
    }
  }, [type, id]);
  
  useEffect(() => {
    if (
      type === 'exclusive' &&
      (rental?.category || '').toUpperCase() === 'BAR AREA RENTAL' &&
      checkIn &&
      checkOut
    ) {
      fetchApartmentUnits();
    }
  }, [type, rental?.category, checkIn, checkOut]);

  const loadRoomDetails = async (roomId: number) => {
    try {
      const room = await RoomService.getRoom(roomId);
      setRoomType(room);
      setAllTiers(room.pricing_tiers || []);
      filterTiers(null, room.pricing_tiers || []); // Initial filter with new tiers
    } catch (error) {
      console.error('Failed to load room details', error);
    }
  };

  const loadRentalDetails = async (rentalId: number) => {
    try {
      const details = await RoomService.getRental(rentalId);
      setRental(details);
      const tiers = (details as any).pricing_tiers || [];
      setAllTiers(tiers);
      setAvailableTiers(tiers);
    } catch (error) {
      console.error('Failed to load rental details', error);
    }
  };

  const fetchUnits = async (roomId: number) => {
    if (!roomId) return;
    try {
      // Reset selections when dates change
      setSelectedUnitId(null);
      setSelectedTierId(null);
      
      const checkInDate = checkIn.toISOString().split('T')[0];
      const checkOutDate = checkOut.toISOString().split('T')[0];

      // Avoid API call if dates are invalid
      if (checkInDate >= checkOutDate) return;

      // Fetch available units
      const units = await RoomService.getAvailableUnits({
        room_type_id: roomId,
        check_in: checkInDate,
        check_out: checkOutDate
      });
      setAvailableUnits(units);
      
      // Ensure tiers are reset
      if (roomType && roomType.id === roomId) {
          filterTiers(null, roomType.pricing_tiers || []);
      }
    } catch (error) {
      console.error('Error fetching units', error);
      setAvailableUnits([]);
    }
  };

  const fetchApartmentUnits = async () => {
    try {
      setSelectedUnitId(null);
      const checkInDate = checkIn.toISOString().split('T')[0];
      const checkOutDate = checkOut.toISOString().split('T')[0];
      if (checkInDate >= checkOutDate) return;
      const units = await RoomService.getAvailableApartmentUnits({
        check_in: checkInDate,
        check_out: checkOutDate
      });
      setAvailableUnits(units);
    } catch (error) {
      console.error('Error fetching apartment units', error);
      setAvailableUnits([]);
    }
  };

  const filterTiers = (unitId: number | null, tiers: Tier[] = allTiers) => {
    if (unitId) {
      setAvailableTiers(tiers.filter(t => t.resort_unit_id === unitId || t.resort_unit_id === null));
    } else {
      setAvailableTiers(tiers.filter(t => t.resort_unit_id === null));
    }
  };

  const handleUnitSelect = (unitId: number | null) => {
    setSelectedUnitId(unitId);
    filterTiers(unitId);
    setSelectedTierId(null); // Reset tier when unit changes
  };

  const handleRoomTypeSelect = (roomId: number) => {
    setSelectedRoomTypeId(roomId);
    setSelectedUnitId(null);
    setSelectedTierId(null);
    // Tiers will be updated by useEffect -> loadRoomDetails
  };

  const calculateTotalPrice = () => {
    let total = 0;
    const nights = Math.max(1, Math.ceil((checkOut.getTime() - checkIn.getTime()) / (1000 * 3600 * 24)));
    
    // Helper to calculate extra person charge
    const calculateExtraPersonCharge = (pax: number, maxCapacity: number, charge: number) => {
       if (pax > maxCapacity) {
          const extra = pax - maxCapacity;
          return extra * charge * nights;
       }
       return 0;
    };

    const bookingType = Array.isArray(type) ? type[0] : type;

    if (bookingType === 'room' && roomType) {
       const isWeekend = checkIn.getDay() === 5 || checkIn.getDay() === 6;
       const basePrice = isWeekend ? Number(roomType.base_price_weekend) : Number(roomType.base_price_weekday);
       total = basePrice * nights;
       
       const maxTierCapacity = allTiers.reduce((max, tier) => Math.max(max, tier.max_guests), 0);
       const canAddExtraPerson = ['DELUXE ROOM', 'GUEST HOUSE'].includes(roomType.category?.toUpperCase());
       if (canAddExtraPerson && Number(paxCount) > maxTierCapacity) {
          total += calculateExtraPersonCharge(Number(paxCount), maxTierCapacity, Number(roomType.extra_person_charge));
       }
    } else if (bookingType === 'exclusive' && rental) {
       let pricePerNight = 0;
       const pax = Number(paxCount);
       
       // Determine max capacity from tiers
       const maxTierCapacity = allTiers.reduce((max, tier) => Math.max(max, tier.max_guests || 0), 0);
       const maxCap = Math.max(maxTierCapacity, rental.capacity_overnight_max || 0);

       // Find Tier based on capped pax (to handle the +1 case)
       const tierPax = Math.min(pax, maxCap);
       const tier = availableTiers.find(t => tierPax >= t.min_guests && tierPax <= t.max_guests);
       
       if (tier) {
          const isWeekend = checkIn.getDay() === 5 || checkIn.getDay() === 6;
          pricePerNight = isWeekend ? Number(tier.price_weekend) : Number(tier.price_weekday);
       } else {
          // Fallback if no tier matches (shouldn't happen with capping, but just in case)
          // Use rental base price (mapped from price_range_min for now as fallback)
           const isWeekend = checkIn.getDay() === 5 || checkIn.getDay() === 6;
           // Note: rental interface has range, but backend has base_price_weekday/weekend
           // Since we don't have base_price in interface, let's use range min/max as proxy or 0
           pricePerNight = Number(rental.price_range_min);
       }
       
       total = pricePerNight * nights;

       // Extra Person Charge
       if (pax > maxCap) {
          total += calculateExtraPersonCharge(pax, maxCap, Number(rental.extra_person_charge || 0));
       }
    }
    
    return total;
  };

  const handleBooking = async () => {
    // Ensure booking type is a string
    const bookingType = Array.isArray(type) ? type[0] : type;

    if (!paxCount) {
      Alert.alert('Error', 'Please enter number of guests');
      return;
    }

    if (bookingType === 'room' && !selectedRoomTypeId) {
        Alert.alert('Error', 'Please select a room type');
        return;
    }

    if (bookingType === 'exclusive') {
      if (!id) {
        Alert.alert('Error', 'Missing rental information');
        return;
      }
      if ((rental?.category || '').toUpperCase() === 'BAR AREA RENTAL' && !selectedUnitId) {
        Alert.alert('Error', 'Please select an Apartment-Style unit');
        return;
      }
    }

    // Validate Max Capacity
    if (bookingType === 'room' && roomType) {
        const maxTierCapacity = allTiers.reduce((max, tier) => Math.max(max, tier.max_guests), 0);
        const canAddExtraPerson = ['DELUXE ROOM', 'GUEST HOUSE'].includes(roomType.category?.toUpperCase());
        const absoluteMax = canAddExtraPerson ? maxTierCapacity + 1 : maxTierCapacity;
        
        if (Number(paxCount) > absoluteMax) {
            Alert.alert('Capacity Exceeded', `Maximum capacity for this room is ${absoluteMax} guests${canAddExtraPerson ? ' (including 1 extra person)' : ''}.`);
            return;
        }
    }
    if (bookingType === 'exclusive' && rental) {
      const maxTierCapacity = allTiers.reduce((max, tier) => Math.max(max, tier.max_guests || 0), 0);
      const maxCap = Math.max(maxTierCapacity, rental.capacity_overnight_max || 0);
      const absoluteMax = maxCap + 1; // Allow 1 extra pax for exclusive rentals

      if (maxCap > 0 && Number(paxCount) > absoluteMax) {
        Alert.alert('Capacity Exceeded', `Maximum capacity for this package is ${maxCap} guests (plus 1 extra person allowed).`);
        return;
      }
    }

    try {
      setLoading(true);
      
      // Check Availability First
      const availabilityCheck = await RoomService.checkAvailability({
        check_in: checkIn.toISOString().split('T')[0],
        check_out: checkOut.toISOString().split('T')[0],
        type: bookingType,
        id: bookingType === 'room' ? (selectedRoomTypeId || Number(id)) : Number(id)
      });

      if (!availabilityCheck.available) {
         Alert.alert('Unavailable', 'The selected dates are no longer available for this accommodation. Please choose different dates.');
         setLoading(false);
         return;
       }

       // Check Unit Availability if specific unit selected
       if (bookingType === 'room' && selectedUnitId) {
         const units = await RoomService.getAvailableUnits({
            room_type_id: selectedRoomTypeId || Number(id),
            check_in: checkIn.toISOString().split('T')[0],
            check_out: checkOut.toISOString().split('T')[0]
         });
         
         const isUnitAvailable = units.some(u => u.id === selectedUnitId);
         if (!isUnitAvailable) {
             Alert.alert('Unit Unavailable', 'The selected unit is no longer available. Please select another unit or "Any Unit".');
             setLoading(false);
             return;
         }
       }
       if (bookingType === 'exclusive' && (rental?.category || '').toUpperCase() === 'BAR AREA RENTAL' && selectedUnitId) {
         const units = await RoomService.getAvailableApartmentUnits({
           check_in: checkIn.toISOString().split('T')[0],
           check_out: checkOut.toISOString().split('T')[0]
         });
         const isUnitAvailable = units.some(u => u.id === selectedUnitId);
         if (!isUnitAvailable) {
           Alert.alert('Unit Unavailable', 'The selected unit is no longer available. Please select another unit.');
           setLoading(false);
           return;
         }
       }
 
       const bookingData = {
        booking_type: bookingType,
        [bookingType === 'room' ? 'room_type_id' : 'exclusive_resort_rental_id']: bookingType === 'room' ? (selectedRoomTypeId || Number(id)) : Number(id),
        check_in: checkIn.toISOString().split('T')[0],
        check_out: checkOut.toISOString().split('T')[0],
        pax_count: Number(paxCount),
        payment_method: 'paymongo',
        resort_unit_id: selectedUnitId,
        pricing_tier_id: selectedTierId
      };

      const response = await BookingService.create(bookingData as any);

      if (response.checkout_url) {
        router.push({
          pathname: '/booking/payment',
          params: { url: response.checkout_url, bookingId: response.booking.id }
        });
      } else {
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

  const maxTierCapacity = allTiers.reduce((max, tier) => Math.max(max, tier.max_guests || 0), 0);
  const canAddExtraPerson = roomType && ['DELUXE ROOM', 'GUEST HOUSE'].includes(roomType.category?.toUpperCase());
  const absoluteMax = canAddExtraPerson ? maxTierCapacity + 1 : maxTierCapacity;

  return (
    <ThemedView style={[styles.container, { paddingTop: insets.top }]}>
      <View style={styles.header}>
        <ThemedText type="title" style={{ fontFamily: Fonts.rounded }}>Book Your Stay</ThemedText>
      </View>

      <ScrollView contentContainerStyle={styles.content}>
        {/* Date Selection */}
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

        {/* Room Type Selection */}
        {type === 'room' && rooms.length > 0 && (
          <View style={styles.section}>
            <ThemedText type="subtitle" style={styles.label}>Select Room Type</ThemedText>
            <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.roomTypeList}>
              {rooms.map((room) => (
                <TouchableOpacity
                  key={room.id}
                  style={[styles.roomTypeCard, selectedRoomTypeId === room.id && styles.selectedOption]}
                  onPress={() => handleRoomTypeSelect(room.id)}
                >
                   <Image
                    source={room.image ? { uri: room.image } : require('@/assets/images/reservation/service-option1.png')}
                    style={styles.roomTypeImage}
                  />
                  <View style={styles.roomTypeContent}>
                      <ThemedText style={[styles.roomTypeTitle, selectedRoomTypeId === room.id && styles.selectedText]}>{room.name}</ThemedText>
                      <ThemedText style={[styles.roomTypePrice, selectedRoomTypeId === room.id && styles.selectedText]}>
                        Weekday: ₱{Number(room.base_price_weekday).toLocaleString()}
                      </ThemedText>
                  </View>
                </TouchableOpacity>
              ))}
            </ScrollView>
          </View>
        )}

        {type === 'exclusive' && rental && (
          <View style={styles.section}>
            <ThemedText type="subtitle" style={styles.label}>{rental.name}</ThemedText>
            <ThemedText style={styles.helperText}>Category: {rental.category}</ThemedText>
          </View>
        )}

        {/* Unit Selection (Optional) - Only if we had units to show */}
        {availableUnits.length > 0 && type === 'room' && (
          <View style={styles.section}>
            <ThemedText type="subtitle" style={styles.label}>Select Unit (Optional)</ThemedText>
            <View style={styles.optionsGrid}>
              <TouchableOpacity 
                style={[styles.optionCard, selectedUnitId === null && styles.selectedOption]}
                onPress={() => handleUnitSelect(null)}
              >
                <ThemedText style={[styles.optionTitle, selectedUnitId === null && styles.selectedText]}>Any Unit</ThemedText>
                <ThemedText style={[styles.optionSub, selectedUnitId === null && styles.selectedText]}>Auto-assign</ThemedText>
              </TouchableOpacity>
              
              {availableUnits.map(unit => (
                <TouchableOpacity 
                  key={unit.id}
                  style={[styles.optionCard, selectedUnitId === unit.id && styles.selectedOption]}
                  onPress={() => handleUnitSelect(unit.id)}
                >
                  <ThemedText style={[styles.optionTitle, selectedUnitId === unit.id && styles.selectedText]}>{unit.name}</ThemedText>
                </TouchableOpacity>
              ))}
            </View>
          </View>
        )}

        {type === 'exclusive' && rental && (rental.category || '').toUpperCase() === 'BAR AREA RENTAL' && (
          <View style={styles.section}>
            <ThemedText type="subtitle" style={styles.label}>Select Apartment-Style Unit (Required)</ThemedText>
            <View style={styles.optionsGrid}>
              {availableUnits.length === 0 ? (
                <ThemedText style={styles.helperText}>No Apartment-Style units available for the selected dates.</ThemedText>
              ) : (
              availableUnits.map(unit => (
                <TouchableOpacity
                  key={unit.id}
                  style={[styles.optionCard, selectedUnitId === unit.id && styles.selectedOption]}
                  onPress={() => setSelectedUnitId(unit.id)}
                >
                  <ThemedText style={[styles.optionTitle, selectedUnitId === unit.id && styles.selectedText]}>{unit.name}</ThemedText>
                </TouchableOpacity>
              )))}
            </View>
          </View>
        )}

        {/* Pricing Tier Selection */}
        {availableTiers.length > 0 && type === 'room' && (
          <View style={styles.section}>
            <ThemedText type="subtitle" style={styles.label}>Select Pricing Tier (Optional)</ThemedText>
            <View style={styles.optionsGrid}>
              <TouchableOpacity 
                style={[styles.optionCard, selectedTierId === null && styles.selectedOption]}
                onPress={() => setSelectedTierId(null)}
              >
                <ThemedText style={[styles.optionTitle, selectedTierId === null && styles.selectedText]}>Auto-calculate</ThemedText>
                <ThemedText style={[styles.optionSub, selectedTierId === null && styles.selectedText]}>Best price based on pax</ThemedText>
              </TouchableOpacity>

              {availableTiers.map(tier => (
                <TouchableOpacity 
                  key={tier.id}
                  style={[styles.optionCard, selectedTierId === tier.id && styles.selectedOption]}
                  onPress={() => setSelectedTierId(tier.id)}
                >
                  <ThemedText style={[styles.optionTitle, selectedTierId === tier.id && styles.selectedText]}>
                    {tier.min_guests}-{tier.max_guests} Pax
                  </ThemedText>
                  <ThemedText style={[styles.optionSub, selectedTierId === tier.id && styles.selectedText]}>
                    ₱{Number(tier.price_weekday).toLocaleString()} / ₱{Number(tier.price_weekend).toLocaleString()}
                  </ThemedText>
                  {tier.resort_unit_id && (
                    <View style={styles.badge}>
                      <Text style={styles.badgeText}>Unit Specific</Text>
                    </View>
                  )}
                </TouchableOpacity>
              ))}
            </View>
          </View>
        )}

        {/* Pricing Tier Selection for Exclusive Rentals */}
        {availableTiers.length > 0 && type === 'exclusive' && (
          <View style={styles.section}>
            <ThemedText type="subtitle" style={styles.label}>Select Pricing Tier</ThemedText>
            <View style={styles.optionsGrid}>
              {availableTiers.map(tier => (
                <TouchableOpacity 
                  key={tier.id}
                  style={[styles.optionCard, selectedTierId === tier.id && styles.selectedOption]}
                  onPress={() => {
                    setSelectedTierId(tier.id);
                    setPaxCount(String(tier.max_guests));
                  }}
                >
                  <ThemedText style={[styles.optionTitle, selectedTierId === tier.id && styles.selectedText]}>
                    {tier.min_guests}-{tier.max_guests} Pax
                  </ThemedText>
                  <ThemedText style={[styles.optionSub, selectedTierId === tier.id && styles.selectedText]}>
                    ₱{Number(tier.price_weekday).toLocaleString()} / ₱{Number(tier.price_weekend).toLocaleString()}
                  </ThemedText>
                </TouchableOpacity>
              ))}
            </View>
          </View>
        )}

        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.label}>Number of Guests</ThemedText>
          <Input
            value={paxCount}
            onChangeText={setPaxCount}
            placeholder={type === 'room'
              ? `Max ${absoluteMax} guests`
              : `Enter guests${maxTierCapacity > 0 ? ` (Max ${maxTierCapacity})` : ''}`
            }
            keyboardType="numeric"
          />
          {canAddExtraPerson && (
            <ThemedText style={styles.helperText}>
              * You can add 1 extra person beyond standard capacity (Total Max: {absoluteMax}). 
              Extra charges apply.
            </ThemedText>
          )}
        </View>

        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.label}>Payment Method</ThemedText>
          <View style={styles.paymentCard}>
            <ThemedText style={styles.paymentText}>PayMongo (Card/GCash)</ThemedText>
            <ThemedText style={styles.paymentSubtext}>Secure payment via PayMongo</ThemedText>
          </View>
        </View>

        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.label}>Estimated Total</ThemedText>
          <ThemedView style={styles.totalCard}>
             <ThemedText style={styles.totalPrice}>₱{estimatedPrice.toLocaleString()}</ThemedText>
             <ThemedText style={styles.totalSubtext}>
               {Math.max(1, Math.ceil((checkOut.getTime() - checkIn.getTime()) / (1000 * 3600 * 24)))} Night(s)
               {Number(paxCount) > maxTierCapacity && ` • Includes Extra Person Charge`}
             </ThemedText>
          </ThemedView>
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
  helperText: {
    color: Colors.gray,
    fontSize: 12,
    marginTop: 4,
  },
  totalCard: {
    padding: Spacing.lg,
    backgroundColor: Colors.primary,
    borderRadius: 12,
    alignItems: 'center',
    marginBottom: Spacing.md,
  },
  totalPrice: {
    color: '#fff',
    fontSize: 24,
    fontWeight: 'bold',
    fontFamily: Fonts.rounded,
  },
  totalSubtext: {
    color: 'rgba(255, 255, 255, 0.8)',
    fontSize: 14,
    marginTop: 4,
  },
  optionsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: Spacing.sm,
  },
  optionCard: {
    width: '48%',
    padding: Spacing.md,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    backgroundColor: '#fff',
    marginBottom: Spacing.xs,
  },
  selectedOption: {
    borderColor: Colors.primary,
    backgroundColor: Colors.primary,
  },
  optionTitle: {
    fontWeight: '600',
    fontSize: 14,
    color: '#0f172a',
  },
  optionSub: {
    fontSize: 12,
    color: '#64748b',
    marginTop: 2,
  },
  selectedText: {
    color: '#fff',
  },
  badge: {
    marginTop: 4,
    backgroundColor: '#e0f2fe',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 4,
    alignSelf: 'flex-start',
  },
  badgeText: {
    fontSize: 10,
    color: '#0369a1',
    fontWeight: '600',
  },
  footer: {
    padding: Spacing.lg,
    borderTopWidth: 1,
    borderTopColor: '#f0f0f0',
    backgroundColor: '#fff',
  },
  roomTypeList: {
    gap: Spacing.md,
    paddingRight: Spacing.lg,
  },
  roomTypeCard: {
    width: 200,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    backgroundColor: '#fff',
    overflow: 'hidden',
    marginRight: Spacing.sm,
  },
  roomTypeImage: {
    width: '100%',
    height: 100,
  },
  roomTypeContent: {
    padding: Spacing.sm,
  },
  roomTypeTitle: {
    fontWeight: '600',
    fontSize: 14,
    color: '#0f172a',
    marginBottom: 4,
  },
  roomTypePrice: {
    fontSize: 12,
    color: '#64748b',
  },
});
