import { User } from "./user.model";

export interface DatabaseConnection {
  id: number;
  userId: number;
  name: string;
  driver: string;
  host: string;
  port: number;
  database: string;
  username: string;
  password: string;
  status: string;
  user?: User;
}