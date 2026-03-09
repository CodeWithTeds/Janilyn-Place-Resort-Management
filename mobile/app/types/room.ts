export interface RoomType {
  id: number;
  name: string;
  description: string;
  base_price_weekday: number;
  base_price_weekend: number;
  min_pax: number;
  max_pax: number;
  extra_person_charge: number;
  image: string | null;
  category: string;
  pricing_tiers?: any[]; // Allow pricing tiers
}

export interface ExclusiveResortRental {
  id: number;
  name: string;
  description: string;
  price_range_min: number;
  price_range_max: number;
  capacity_overnight_min: number;
  capacity_overnight_max: number;
  extra_person_charge: number;
  cooking_fee: number;
  image: string | null;
  category?: string;
  pricing_tiers?: any[];
}

export interface RoomAvailability {
  available: boolean;
}
