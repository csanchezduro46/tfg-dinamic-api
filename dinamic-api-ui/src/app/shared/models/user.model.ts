export interface User {
  id: number;
  name: string;
  email: string;
  password?: string;
  roles?: any[];
  email_verified_at?: Date;
}