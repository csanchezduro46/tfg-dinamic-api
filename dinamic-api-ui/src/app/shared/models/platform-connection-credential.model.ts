import { PlatformConnection } from "./platform-connection.model";
import { PlatformNecessaryKey } from "./platform-necessary-key.model";

export interface PlatformConnectionCredential {
  id: number;
  platformConnectionId: number;
  necessaryKeyId: number;
  value?: string;
  platformConnection?: PlatformConnection;
  necessaryKey?: PlatformNecessaryKey;
}