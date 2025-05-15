import { PlatformConnection } from "./platform-connection.model";
import { PlatformNecessaryKey } from "./platform-necessary-key.model";
import { PlatformVersion } from "./platform-version.model";

export interface Platform {
  id: number;
  name: string;
  slug: string;
  versions?: PlatformVersion[];
  connections?: PlatformConnection[];
  necessaryKeys?: PlatformNecessaryKey[];
}