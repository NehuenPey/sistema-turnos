export interface Appointment {
  id: number;
  client: string;
  date: string;
  time: string;
  status: 'pending' | 'confirmed' | 'cancelled';
}
