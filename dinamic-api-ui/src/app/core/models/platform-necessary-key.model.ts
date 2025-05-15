import { PlatformConnectionCredential } from "./platform-connection-credential.model";
import { Platform } from "./platform.model";

export interface PlatformNecessaryKey {
  id: number;
  platformId: number;
  key: string;
  label?: string;
  required: boolean;
  platform?: Platform;
  necessaryKey?: PlatformConnectionCredential[];
}