import { ApiCallMapping } from "./api-call-mapping.model";

export interface Execution {
    id: number;
    apiCallMappingId: number;
    status: string;
    executionType: string;
    responseLog?: any;
    startedAt?: Date;
    finishedAt?: Date;
    repeat: string;
    cronExpression?: string;
    lastExecutedAt?: Date;
    apiCallMapping?: ApiCallMapping;
}