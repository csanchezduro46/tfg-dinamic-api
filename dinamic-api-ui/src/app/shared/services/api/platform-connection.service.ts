import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { catchError, Observable } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { ErrorHandlerService } from '../errors/error-handler.service';

@Injectable({ providedIn: 'root' })
export class PlatformConnectionService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    getAll(): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/connections`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    getMine(): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/connections/me`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    get(id: number): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/connections/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    create(data: any): Observable<any> {
        return this.http.post(`${this.baseUrl}/api/connections`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    update(id: number, data: any) {
        return this.http.put(`${this.baseUrl}/api/connections/${id}`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    delete(id: number) {
        return this.http.delete(`${this.baseUrl}/api/connections/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
