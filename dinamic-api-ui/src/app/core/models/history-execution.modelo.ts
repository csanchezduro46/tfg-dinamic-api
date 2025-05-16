import { Execution } from "./execution.model";

export interface HistoryExecution {
    id: number;
    executionId: number;
    status: string;
    log?: any;
    launchedAt?: Date;
    execution?: Execution;
}