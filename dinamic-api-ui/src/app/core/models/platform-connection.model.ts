import { PlatformConnectionCredential } from "./platform-connection-credential.model";
import { PlatformVersion } from "./platform-version.model";
import { User } from "./user.model";

export interface PlatformConnection {
  id: number;
  userId: number;
  platformVersionId: number;
  name: string;
  storeUrl: string;
  status: string;
  lastCheckedAt?: Date;
  config?: any;
  user?: User;
  version?: PlatformVersion;
  credentials?: PlatformConnectionCredential[];
}