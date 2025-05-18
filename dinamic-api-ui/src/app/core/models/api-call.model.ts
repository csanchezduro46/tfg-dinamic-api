import { ApiCallGroup } from "./api-call-group.model";
import { PlatformVersion } from "./platform-version.model";

export interface ApiCall {
  id: number;
  platformVersionId: number;
  name: string;
  groupId: number;
  endpoint: string;
  method: string;
  request_type?: string;
  response_type?: string;
  payload_example?: any;
  response_example?: Array<any>;
  description?: string;
  version?: PlatformVersion;
  group?: ApiCallGroup;
}