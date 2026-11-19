import {
  Car,
  Wrench,
  Shield,
  Battery,
  Settings,
  Truck,
  Phone,
  Clock,
  CheckCircle,
  MapPin,
  Star
} from 'lucide-react';
import {
  image1TowingVehicle,
  image2SuvKiaSportage2024,
  image3SedanToyotaCorolla,
  image4VanHyundaiH12022,
  image5EconomyNissanAlmera2021,
  image6LuxuryMercedesBenz,
  image7PickupToyotaHilux2023,
  image8HeavyDutyRecoveryVehicle,
  image9CarBatteryMaintenance,
  image10EngineOilFluidsMaintenance,
  image11BrakePartsDiskMaintenance,
  image12TyreMaintenance,
  image13FiltersMaintenance,
  image14SuspensionPartsMaintenance,
  image15CarLightingMaintenance,
  image16Accessories,
} from '@/assets/images';

export const HERO_SLIDES = [
  {
    id: 1,
    headline: '24/7 Emergency Towing Services',
    subheadline: 'Fast response, safe car transport, affordability, and reliability. Call us anytime, anywhere.',
    buttons: [
      { label: 'Call Now: 0244753849', primary: true, href: 'tel:0244753849' },
      { label: 'WhatsApp Us', primary: false, href: 'https://wa.me/233244753849' }
    ],
    image: image8HeavyDutyRecoveryVehicle,
  },
  {
    id: 2,
    headline: 'Accident Recovery & Roadside Assistance',
    subheadline: 'Professional winch-outs, flat tire changes, jump starts, and safe off-road recovery.',
    buttons: [
      { label: 'Request Assistance', primary: true, href: 'tel:0244753849' }
    ],
    image: image9CarBatteryMaintenance,
  },
  {
    id: 3,
    headline: 'Flatbed Towing & Car Transport',
    subheadline: 'Secure and damage-free transport for luxury cars, SUVs, and specialized cars.',
    buttons: [
      { label: 'Call for Transport', primary: true, href: '/contact' }
    ],
    image: image10EngineOilFluidsMaintenance,
  },
  {
    id: 4,
    headline: 'Premium Car Rentals',
    subheadline: 'Choose from our wide range of well-maintained cars for business or leisure.',
    buttons: [
      { label: 'Browse Cars', primary: true, href: '/rental' }
    ],
    image: image11BrakePartsDiskMaintenance,
  },
  {
    id: 5,
    headline: 'Professional Auto Repairs',
    subheadline: 'Expert diagnostics and repairs by certified mechanics you can trust.',
    buttons: [
      { label: 'Call for Repair', primary: true, href: 'tel:0244753849' },
      { label: 'WhatsApp for Repair', primary: false, href: 'https://wa.me/233244753849' }
    ],
    image: image12TyreMaintenance,
  },
];

export const QUICK_ACTIONS = [
  { id: 1, title: 'Request Towing', description: '24/7 emergency response', icon: Truck },
  { id: 2, title: 'Rent a Car', description: 'Flexible daily & weekly rates', icon: Car },
  { id: 3, title: 'Buy a Car', description: 'New & premium used cars', icon: Shield },
  { id: 4, title: 'Call for Repairs', description: 'Certified expert mechanics', icon: Wrench },
  { id: 6, title: 'Shop Auto Parts', description: 'Genuine replacement parts', icon: Settings },
];

export const STATS = [
  { label: 'Years of Experience', value: 15, suffix: '+' },
  { label: 'Cars Repaired', value: 25000, suffix: '+' },
  { label: 'Happy Customers', value: 10000, suffix: '+' },
  { label: 'Emergency Calls Completed', value: 5000, suffix: '+' },
];

export const SERVICES = [
  { id: 1, title: 'Emergency Towing', desc: 'Fast response towing anywhere, anytime.', icon: Truck, image: image1TowingVehicle },
  { id: 2, title: 'Car Recovery', desc: 'Safe off-road and accident recovery.', icon: Shield, image: image1TowingVehicle },
  { id: 3, title: 'Car Rental', desc: 'Wide range of cars for rent.', icon: Car, image: image13FiltersMaintenance },
  { id: 4, title: 'Car Sales', desc: 'Quality new and used cars.', icon: Car, image: image14SuspensionPartsMaintenance },
  { id: 5, title: 'Auto Repairs', desc: 'Comprehensive mechanical repairs. Buy Batteries available.', icon: Wrench, image: image15CarLightingMaintenance },
  { id: 6, title: 'Preventive Maintenance', desc: 'Keep your car running smoothly, including advanced Car Diagnostics.', icon: Settings, image: image10EngineOilFluidsMaintenance },
  { id: 8, title: 'Spray Painting', desc: 'Premium automotive painting.', icon: Shield, image: image6LuxuryMercedesBenz },
  { id: 10, title: 'Genuine Auto Parts', desc: 'High-quality OEM parts.', icon: Settings, image: image16Accessories },
  { id: 12, title: 'Fleet Services', desc: 'Commercial car and fleet management.', icon: Truck, image: image4VanHyundaiH12022 },
];

export const CARS = [
  // New Cars
  { id: 1, type: 'New', name: '2024 Luxury Sedan', price: 'GH₵ 540,000', fuel: 'Hybrid', transmission: 'Automatic', year: 2024, mileage: '0 km', image: image3SedanToyotaCorolla },
  { id: 2, type: 'New', name: '2024 Premium SUV', price: 'GH₵ 780,000', fuel: 'Petrol', transmission: 'Automatic', year: 2024, mileage: '25 km', image: image2SuvKiaSportage2024 },
  { id: 3, type: 'New', name: '2024 Executive Pickup', price: 'GH₵ 620,000', fuel: 'Diesel', transmission: 'Automatic', year: 2024, mileage: '15 km', image: image7PickupToyotaHilux2023 },
  { id: 4, type: 'New', name: '2024 Passenger Van', price: 'GH₵ 690,000', fuel: 'Diesel', transmission: 'Manual', year: 2024, mileage: '40 km', image: image4VanHyundaiH12022 },

  // Foreign Used Cars
  { id: 5, type: 'Foreign Used', name: '2020 Midsize Sedan', price: 'GH₵ 264,000', fuel: 'Petrol', transmission: 'Automatic', year: 2020, mileage: '72,000 km', image: image3SedanToyotaCorolla },
  { id: 6, type: 'Foreign Used', name: '2019 Luxury SUV', price: 'GH₵ 450,000', fuel: 'Diesel', transmission: 'Automatic', year: 2019, mileage: '65,000 km', image: image2SuvKiaSportage2024 },
  { id: 7, type: 'Foreign Used', name: '2021 Premium Hatchback', price: 'GH₵ 210,000', fuel: 'Petrol', transmission: 'Automatic', year: 2021, mileage: '48,000 km', image: image5EconomyNissanAlmera2021 },
  { id: 8, type: 'Foreign Used', name: '2018 Heavy Duty Pickup', price: 'GH₵ 340,000', fuel: 'Diesel', transmission: 'Manual', year: 2018, mileage: '92,000 km', image: image7PickupToyotaHilux2023 },

  // Ghana Used Cars
  { id: 9, type: 'Ghana Used', name: '2018 Compact Hatchback', price: 'GH₵ 144,000', fuel: 'Petrol', transmission: 'Manual', year: 2018, mileage: '136,000 km', image: image5EconomyNissanAlmera2021 },
  { id: 10, type: 'Ghana Used', name: '2015 Family SUV', price: 'GH₵ 185,000', fuel: 'Petrol', transmission: 'Automatic', year: 2015, mileage: '185,000 km', image: image2SuvKiaSportage2024 },
  { id: 11, type: 'Ghana Used', name: '2017 Commuter Sedan', price: 'GH₵ 120,000', fuel: 'Petrol', transmission: 'Manual', year: 2017, mileage: '162,000 km', image: image3SedanToyotaCorolla },
  { id: 12, type: 'Ghana Used', name: '2014 Utility Pickup', price: 'GH₵ 165,000', fuel: 'Diesel', transmission: 'Manual', year: 2014, mileage: '210,000 km', image: image7PickupToyotaHilux2023 },
];
export const VEHICLES = CARS; // Backward compatibility alias
export const RENTAL_CATEGORIES = [
  { id: 1, name: 'Economy', daily: 'GH₵ 400', weekly: 'GH₵ 2,500', monthly: 'GH₵ 9,000', seats: 4, trans: 'Auto/Manual', fuel: 'Petrol', image: image5EconomyNissanAlmera2021 },
  { id: 2, name: 'Sedan', daily: 'GH₵ 600', weekly: 'GH₵ 3,800', monthly: 'GH₵ 14,000', seats: 5, trans: 'Automatic', fuel: 'Petrol/Hybrid', image: image3SedanToyotaCorolla },
  { id: 3, name: 'SUV', daily: 'GH₵ 900', weekly: 'GH₵ 5,800', monthly: 'GH₵ 21,000', seats: 7, trans: 'Automatic', fuel: 'Petrol/Diesel', image: image2SuvKiaSportage2024 },
  { id: 4, name: 'Pickup', daily: 'GH₵ 800', weekly: 'GH₵ 5,000', monthly: 'GH₵ 18,000', seats: 5, trans: 'Automatic', fuel: 'Diesel', image: image7PickupToyotaHilux2023 },
  { id: 5, name: 'Luxury', daily: 'GH₵ 1,500', weekly: 'GH₵ 9,500', monthly: 'GH₵ 35,000', seats: 5, trans: 'Automatic', fuel: 'Premium', image: image6LuxuryMercedesBenz },
  { id: 6, name: 'Van', daily: 'GH₵ 1,100', weekly: 'GH₵ 7,000', monthly: 'GH₵ 25,000', seats: 12, trans: 'Auto/Manual', fuel: 'Diesel', image: image4VanHyundaiH12022 },
];

export const PARTS_CATEGORIES = [
  'Car Batteries', 'Engine Oil', 'Brake Pads', 'Tires',
  'Filters', 'Suspension Parts', 'Lighting', 'Accessories'
];

export const WHY_CHOOSE_US = [
  { title: '24/7 Emergency Support', icon: Clock },
  { title: 'Fast Response Time', icon: Truck },
  { title: 'Certified Technicians', icon: Shield },
  { title: 'Affordable Pricing', icon: Settings },
  { title: 'Genuine Parts', icon: Settings },
  { title: 'Modern Equipment', icon: Settings },
  { title: 'Nationwide Coverage', icon: MapPin },
  { title: 'Customer Satisfaction', icon: CheckCircle },
];

export const FAQS = [
  { q: 'How fast can you respond to a towing emergency?', a: 'Our average response time is under 30 minutes within city limits.' },
  { q: 'What documents do I need to rent a car?', a: 'You will need a valid driver\'s license, proof of identity, and a valid credit card for the security deposit.' },
  { q: 'Do you offer warranties on auto repairs?', a: 'Yes, we offer a 6-month or 6,000-mile warranty on all our repair services and parts.' },
  { q: 'Can I finance a car purchase?', a: 'Yes, we partner with several major banks to offer flexible financing options.' },
];
