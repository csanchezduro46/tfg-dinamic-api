import { ApiCallMapping } from "./api-call-mapping.model";

export interface ApiCallMappingField {
    id: number;
    apiCallMappingId: number;
    sourceField: string;
    targetField: string;
    apiCallMapping?: ApiCallMapping;
}