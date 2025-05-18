import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { catchError, Observable } from 'rxjs';
import { ErrorHandlerService } from '../errors/error-handler.service';
import { environment } from '../../../../environments/environment';

@Injectable({ providedIn: 'root' })
export class PlatformService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    getAll(): Observable<any> {
        return this.http.get(`${this.baseUrl}/api/platforms`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    create(data: any): Observable<any> {
        return this.http.post(`${this.baseUrl}/api/platforms`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    update(id: number, data: any) {
        return this.http.put(`${this.baseUrl}/api/platforms/${id}`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    delete(id: number) {
        return this.http.delete(`${this.baseUrl}/api/platforms/${id}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
