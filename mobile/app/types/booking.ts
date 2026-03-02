export interface Booking {
  id: number;
  user_id: number;
  room_type_id: number | null;
  exclusive_resort_rental_id: number | null;
  check_in: string;
  check_out: string;
  pax_count: number;
  total_price: string;
  status: string;
  payment_status: string;
  payment_method: string;
  created_at: string;
}

export interface CreateBookingData {
  booking_type: 'room' | 'exclusive';
  room_type_id?: number;
  exclusive_resort_rental_id?: number;
  resort_unit_id?: number | null;
  pricing_tier_id?: number | null;
  check_in: string;
  check_out: string;
  pax_count: number;
  payment_method: 'paymongo' | 'cash';
}
