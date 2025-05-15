import { ApiCall } from "./api-call.model";
import { DatabaseConnection } from "./database-connection.model";
import { User } from "./user.model";

export interface ApiCallMapping {
    id: number;
    userId: number;
    name: string;
    direction: string;
    description?: string;
    sourceApiCallId?: number;
    sourceDbConnectionId?: number;
    sourceTable?: string;
    targetApiCallId?: number;
    targetDbConnectionId?: number;
    targetTable?: string;
    user?: User;
    sourceApiCall?: ApiCall;
    sourceDbConnection?: DatabaseConnection;
    targetApiCall?: ApiCall;
    targetDbConnection?: DatabaseConnection;
}