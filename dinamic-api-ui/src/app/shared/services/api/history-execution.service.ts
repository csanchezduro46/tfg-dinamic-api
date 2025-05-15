import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class HistoryExecutionService {
    constructor(private readonly http: HttpClient) { }

    getLogs(executionId: number, status?: string) {
        const url = status
            ? `/api/executions/${executionId}/logs?status=${status}`
            : `/api/executions/${executionId}/logs`;

        return this.http.get(url);
    }
}
