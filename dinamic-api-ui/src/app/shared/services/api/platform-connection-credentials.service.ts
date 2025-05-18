import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';
import { ErrorHandlerService } from '../errors/error-handler.service';
import { catchError } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class PlatformConnectionCredentialsService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    get(id: number) {
        return this.http.get(`${this.baseUrl}/api/connections/${id}/credentials`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    create(id: number, data: any) {
        return this.http.post(`${this.baseUrl}/api/connections/${id}/credentials`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    update(id: number, data: any) {
        return this.http.put(`${this.baseUrl}/api/connections/${id}/credentials`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    delete(id: number) {
        return this.http.delete(`${this.baseUrl}/api/connections/${id}/credentials`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    deleteOne(id: number, keyId: number) {
        return this.http.delete(`${this.baseUrl}/api/connections/${id}/credentials/${keyId}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    test(id: number) {
        return this.http.get(`${this.baseUrl}/api/connections/${id}/test`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
