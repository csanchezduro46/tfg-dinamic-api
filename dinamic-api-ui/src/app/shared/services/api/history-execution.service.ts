import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';
import { ErrorHandlerService } from '../errors/error-handler.service';
import { catchError } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class HistoryExecutionService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }


    getLogs(executionId: number, status?: string) {
        const url = status
            ? `${this.baseUrl}/api/executions/${executionId}/logs?status=${status}`
            : `${this.baseUrl}/api/executions/${executionId}/logs`;

        return this.http.get(url).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
