import { ApiCall } from "./api-call.model";
import { Platform } from "./platform.model";

export interface PlatformVersion {
  id: number;
  platformId: number;
  version: string;
  description?: string;
  platform?: Platform;
  apiCalls?: ApiCall[];
}