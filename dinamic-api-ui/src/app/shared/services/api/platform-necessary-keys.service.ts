import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';
import { ErrorHandlerService } from '../errors/error-handler.service';
import { catchError } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class PlatformNecessaryKeysService {
    private readonly baseUrl = environment.apiUrl;

    constructor(private readonly http: HttpClient, private readonly errorHandler: ErrorHandlerService) { }

    get(platformId: number) {
        return this.http.get(`${this.baseUrl}/api/platforms/${platformId}/necessary-keys`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    create(platformId: number, data: any) {
        return this.http.post(`${this.baseUrl}/api/platforms/${platformId}/necessary-keys`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    update(platformId: number, keyId: number, data: any) {
        return this.http.put(`${this.baseUrl}/api/platforms/${platformId}/necessary-keys/${keyId}`, data).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }

    delete(platformId: number, keyId: number) {
        return this.http.delete(`${this.baseUrl}/api/platforms/${platformId}/necessary-keys/${keyId}`).pipe(
            catchError(err => this.errorHandler.handle(err))
        );
    }
}
