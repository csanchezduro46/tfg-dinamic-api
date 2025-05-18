import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';
import { ErrorHandlerService } from '../errors/error-handler.service';
import { catchError, Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ExecutionService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    getAll(): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/executions`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getScheduled(): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/executions?type=scheduled`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getByMapping(id: number) {
        return this.http.get(`${this.baseUrl}/api/executions/mappings/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    create(mappingId: number, data: any) {
        return this.http.post(`${this.baseUrl}/api/executions/mappings/${mappingId}`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    update(id: number, data: any) {
        return this.http.put(`${this.baseUrl}/api/executions/${id}`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    delete(id: number) {
        return this.http.delete(`${this.baseUrl}/api/executions/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    launch(id: number) {
        return this.http.post(`${this.baseUrl}/api/executions/${id}/launch`, {}).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
